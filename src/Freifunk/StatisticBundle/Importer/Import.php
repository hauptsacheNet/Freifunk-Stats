<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marco
 * Date: 28.08.13
 * Time: 09:54
 * To change this template use File | Settings | File Templates.
 */

namespace Freifunk\StatisticBundle\Importer;


use Assetic\Factory\Resource\ResourceInterface;
use Doctrine\ORM\EntityManager;
use Freifunk\StatisticBundle\Entity\Link;
use Freifunk\StatisticBundle\Entity\LinkRepository;
use Freifunk\StatisticBundle\Entity\Node;
use Freifunk\StatisticBundle\Entity\NodeRepository;
use Freifunk\StatisticBundle\Entity\NodeStat;
use Freifunk\StatisticBundle\Entity\NodeStatRepository;
use Freifunk\StatisticBundle\Entity\UpdateLog;
use Freifunk\StatisticBundle\Entity\UpdateLogRepository;
use Freifunk\StatisticBundle\Service\JsonImporter;
use Symfony\Component\Validator\Validator;

/**
 * This class represents an import action. This means it should be created for one import only.
 *
 * @package Freifunk\StatisticBundle\Importer
 */
class Import
{
    /** @var EntityManager */
    private $em;
    /** @var Validator */
    private $validator;
    /** @var array */
    private $data;

    /** @var UpdateLogRepository */
    private $logRep;
    /** @var NodeRepository */
    private $nodeRep;
    /** @var NodeStatRepository */
    private $nodeStatRep;
    /** @var LinkRepository */
    private $linkRep;

    /** @var UpdateLog */
    private $log;
    /** @var Node[] */
    private $nodesInFile = array();
    /** @var Node[] */
    private $nodesToAdd = array();
    /** @var Link[] */
    private $linksInFile = array();
    /** @var Link[] */
    private $linksToAdd = array();
    /** @var NodeStat[] */
    private $statsToAdd = array();

    public function __construct(EntityManager $em, Validator $validator, $string)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->logRep = $this->em->getRepository('FreifunkStatisticBundle:UpdateLog');
        $this->nodeRep = $this->em->getRepository('FreifunkStatisticBundle:Node');
        $this->nodeStatRep = $this->em->getRepository('FreifunkStatisticBundle:NodeStat');
        $this->linkRep = $this->em->getRepository('FreifunkStatisticBundle:Link');
        $this->string = $string;
    }

    public function execute()
    {
        // create log
        $this->log = new UpdateLog();

        try {
            // prepare the data our of the resource
            $this->log->setFileSize(strlen($this->string));
            if ($this->log->getFileSize() == 0) {
                throw new ImportException(null, 'The given resource is empty or not available');
            }
            $this->data = json_decode($this->string, true);
            if ($this->data === null) {
                throw new ImportException(null, 'The JSON was not well formated (' . json_last_error() . ')');
            }

            $this->testData($this->data, array(
                'links' => 'array',
                'meta' => 'array',
                'nodes' => 'array'
            ));

            $this->handleMetaData();

            $this->parseNodes();
            $this->cleanupDatabaseNodes();
            $this->importNodes();
            $this->em->flush();

            $this->parseLinks();
            $this->cleanupDatabaseLinks();
            $this->importLinks();
            $this->em->flush();
        } catch (ImportException $e) {
            $this->log->addMessage($e->getMessage());
        }

        // last log updates
        $this->log->finish();
        $this->em->persist($this->log);
        $this->em->flush();
        return $this->log;
    }

    /**
     * Logs the meta data
     */
    private function handleMetaData()
    {
        $this->testData($this->data['meta'], array('timestamp' => 'string'));
        $this->log->setFileTime(new \DateTime($this->data['meta']['timestamp']));
        $lastLog = $this->logRep->getLastEntry();
        if ($lastLog != null && $lastLog->getFileTime()->getTimestamp() >= $this->log->getFileTime()->getTimestamp()) {
            throw new ImportException(null,
                'The specified file with the time '
                . $this->log->getFileTime()->format('Y-m-d H:i:s')
                . ' is already parsed'
            );
        }
    }

    /**
     * Creates all node instances of the file
     */
    private function parseNodes()
    {
        // create full list of nodes
        foreach ($this->data['nodes'] as $index => $rawNode) {
            try {
                $fileNode = $this->createNodeInstance($rawNode);
                $this->nodesInFile[$fileNode->getMac()] = $fileNode;
            } catch (ImportException $e) {
                $this->log->addMessage(
                    'error in node ' . $index . ' with property '
                    . $e->getWrongKey() . ' (' . $e->getMessage() . ')'
                );
            }
        }

        // filter nodes that already exist
        $this->nodesToAdd = $this->nodesInFile;
        $qb = $this->nodeStatRep->createQueryBuilder('s');
        $qb->join('s.node', 'n');
        $qb->select('n.id, n.mac, s.online, s.clientCount');
        $qb->orderBy('s.time', 'DESC');
        if (!empty($this->nodesInFile)) {
            $qb->andWhere($qb->expr()->in('n.mac', array_keys($this->nodesInFile)));
        }
        // loop nodes that were already found in the database to remove them from our add list
        foreach ($qb->getQuery()->getArrayResult() as $inDbNode) {
            $mac = $inDbNode['mac'];
            if (array_key_exists($mac, $this->nodesToAdd)) {
                $fileNode = $this->nodesToAdd[$mac];
                unset($this->nodesToAdd[$mac]);
                $this->log->nodePreserved();
                // replace the node in the file with a relation one that represents the database one
                $this->nodesInFile[$mac] = $this->em->getReference('FreifunkStatisticBundle:Node', $inDbNode['id']);

                // if the given stat is not identical to the current one in the database add it
                /** @var NodeStat $stat */
                $stat = $fileNode->getStats()->last();
                if ($stat->getOnline() != $inDbNode['online'] || $stat->getClientCount() != $inDbNode['clientCount']) {
                    $stat->setNode($this->nodesInFile[$mac]);
                    $this->statsToAdd[] = $stat;
                    $this->log->statusUpdated();
                }
            }
        }
    }

    /**
     * Creates a node instances of the json part
     *
     * @param array $data
     * @return Node
     */
    private function createNodeInstance(array $data)
    {
        $this->testData($data, array(
            'flags' => 'array',
            'geo' => null,
            'id' => 'string',
            'macs' => 'string',
            'name' => 'string'
        ));

        $node = new Node();
        $node->setTime($this->log->getFileTime());
        $node->setNodeName($data['name'] != '' ? $data['name'] : null);
        $node->setMac($data['id']);

        if ($data['geo'] != null) {
            $node->setLatitude($data['geo'][0]);
            $node->setLongitude($data['geo'][1]);
        }

        // now add the node status to the existing node
        $status = new NodeStat();
        $status->setTime($this->log->getFileTime());
        $status->setNode($node);
        $status->setOnline((bool)$data['flags']['online']);
        $status->setClientCount(count(explode(', ', $data['macs'])));
        $this->validate($status);
        $node->addStat($status);

        $this->validate($node);
        return $node;
    }

    /**
     * Creates all link instances of the file
     */
    private function parseLinks()
    {
        // create full list of links
        foreach ($this->data['links'] as $index => $rawLink) {
            try {
                $link = $this->createLinkInstance($rawLink);
                $this->linksInFile[$link->getMacString()] = $link;
            } catch (ImportException $e) {
                $this->log->addMessage(
                    'error in link ' . $index . ' with property '
                    . $e->getWrongKey() . ' (' . $e->getMessage() . ')'
                );
            }
        }

        // filter links that already exist
        $this->linksToAdd = $this->linksInFile;
        $qb = $this->linkRep->createQueryBuilder('l');
        $qb->select('l.id, target.mac AS tMAC, source.mac AS sMAC');
        $qb->join('l.target', 'target');
        $qb->join('l.source', 'source');
        $qb->andWhere($qb->expr()->isNull('l.closeTime'));

        if (!empty($this->linksInFile)) {
            $linkSearch = $qb->expr()->orX();
            foreach ($this->linksInFile as $link) {
                $linkSearch->add($qb->expr()->andX(
                    $qb->expr()->eq('target.mac', $qb->expr()->literal($link->getTarget()->getMac())),
                    $qb->expr()->eq('source.mac', $qb->expr()->literal($link->getSource()->getMac()))
                ));
            }
            $qb->andWhere($linkSearch);
        }

        // now remove links that are up to date already
        foreach ($qb->getQuery()->getArrayResult() as $inDbLink) {
            $linkId = $inDbLink['tMAC'] . '-' . $inDbLink['sMAC'];
            if (array_key_exists($linkId, $this->linksToAdd)) {
                unset($this->linksToAdd[$linkId]);
                $this->log->linkPreserved();
                // replace the link in the file with a relation one that represents the database one
                $this->nodesInFile[$linkId] = $this->em->getReference('FreifunkStatisticBundle:Link', $inDbLink['id']);
            }
        }

    }

    /**
     * Creates a Link instance out of the json part
     *
     * @param array $data
     * @return Link
     * @throws ImportException
     */
    private function createLinkInstance(array $data)
    {
        $this->testData($data, array(
            'id' => 'string',
            'quality' => 'string',
            'source' => 'integer',
            'target' => 'integer',
            'type' => 'string'
        ));

        $link = new Link();
        $link->setOpenTime($this->log->getFileTime());

        list($target, $source) = explode('-', strtoupper($data['id']));
        if (array_key_exists($target, $this->nodesInFile) && array_key_exists($source, $this->nodesInFile)) {
            $link->setTarget($this->nodesInFile[$target]);
            $link->setSource($this->nodesInFile[$source]);
        } else {
            throw new ImportException('source, target', $source . ' ' . $target);
        }

        $link->setType($data['type'] == 'client' ? Link::CLIENT : Link::VPN);
        $link->setQuality($data['quality']);

        $this->validate($link);
        return $link;
    }

    /**
     * removes all nodes that weren't found in the file
     */
    private function cleanupDatabaseNodes()
    {
        $qb = $this->nodeRep->createQueryBuilder('n');
        if (!empty($this->nodesInFile)) {
            $qb->andWhere($qb->expr()->notIn('n.mac', array_keys($this->nodesInFile)));
        }
        /** @var Node $node */
        foreach ($qb->getQuery()->getResult() as $node) {
            $this->em->remove($node);
            $this->log->nodeRemoved();
        }
    }

    /**
     * closes all links that don't exist in the file anymore.
     */
    private function cleanupDatabaseLinks()
    {
        $qb = $this->linkRep->createQueryBuilder('l')->update();
        if (!empty($this->linksInFile)) {
            $expr = $qb->expr()->andX();
            foreach ($this->linksInFile as $link) {
                $expr->add($qb->expr()->orX(
                    $qb->expr()->neq('l.source', $qb->expr()->literal($link->getSource()->getId())),
                    $qb->expr()->neq('l.target', $qb->expr()->literal($link->getTarget()->getId()))
                ));
            }
            $qb->andWhere($expr);
        }
        $qb->andWhere($qb->expr()->isNull('l.closeTime'));
        $qb->set('l.closeTime', $qb->expr()->literal(date('Y-m-d H:i:s')));
        $this->log->setLinksRemoved($qb->getQuery()->execute());
    }

    /**
     * Gives all nodes to the entity manager
     */
    private function importNodes()
    {
        foreach ($this->nodesToAdd as $node) {
            $this->em->persist($node);
            $this->log->nodeAdded();
            $this->log->statusUpdated(); // because every node has one status
        }
        foreach ($this->statsToAdd as $stat) {
            $this->em->persist($stat);
            $this->log->statusUpdated();
        }
    }

    /**
     * gives all links to the entity manager
     */
    private function importLinks()
    {
        foreach ($this->linksToAdd as $link) {
            $this->em->persist($link);
            $this->log->linkAdded();
        }
    }

    /**
     * @param array $data
     * @param array $keys
     * @throws ImportException
     */
    private static function testData(array &$data, array $keys)
    {
        // check if there is everything
        foreach ($keys as $key => $type) {
            if (!array_key_exists($key, $data) || ($type != null && gettype($data[$key]) != $type)) {
                throw new ImportException($key);
            }
        }
    }

    /**
     * @param $entity
     * @throws ImportException
     */
    private function validate($entity)
    {
        $violations = $this->validator->validate($entity);
        if ($violations->count() > 0) {
            $violation = $violations->get(0);
            throw new ImportException($violation->getPropertyPath(), $violation->getMessage());
        }
    }
}