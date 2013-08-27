<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marco
 * Date: 26.08.13
 * Time: 16:15
 * To change this template use File | Settings | File Templates.
 */

namespace Freifunk\StatisticBundle\Service;


use Assetic\Factory\Resource\FileResource;
use Doctrine\Common\Persistence\ObjectManager;
use Freifunk\StatisticBundle\Entity\Link;
use Freifunk\StatisticBundle\Entity\LinkRepository;
use Freifunk\StatisticBundle\Entity\Node;
use Freifunk\StatisticBundle\Entity\NodeRepository;
use Freifunk\StatisticBundle\Entity\NodeStat;
use Freifunk\StatisticBundle\Entity\NodeStatRepository;
use Freifunk\StatisticBundle\Entity\UpdateLog;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator;

class JsonImporter
{
    /** @var ObjectManager */
    private $em;
    /** @var Validator */
    private $validator;

    /** @var NodeRepository */
    private $nodeRep;
    /** @var NodeStatRepository */
    private $nodeStatRep;
    /** @var LinkRepository */
    private $linkRep;

    /**
     * @param ObjectManager $em
     * @param Validator $validator
     */
    public function __construct(ObjectManager $em, Validator $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->nodeRep = $this->em->getRepository('FreifunkStatisticBundle:Node');
        $this->nodeStatRep = $this->em->getRepository('FreifunkStatisticBundle:NodeStat');
        $this->linkRep = $this->em->getRepository('FreifunkStatisticBundle:Link');
    }

    /**
     * @param FileResource $file
     * @return UpdateLog
     */
    public function fromFile(FileResource $file)
    {
        return $this->fromJSON($file->getContent());
    }

    /**
     * @param string $string
     * @return UpdateLog
     */
    public function fromJSON($string)
    {
        $data = json_decode($string, true);
        $log = new UpdateLog();
        $log->setFileSize(strlen($string));
        unset($string); // save some memory
        if ($data === null) {
            $log->addMessage('JSON file was invalid');
            $this->em->persist($log);
            $this->em->flush();
            return $log;
        } else {
            return $this->fromData($data);
        }
    }

    /**
     * @param array $data
     * @param UpdateLog $updateLog
     * @return UpdateLog
     */
    public function fromData(array &$data, UpdateLog $updateLog = null)
    {
        $log = $updateLog ? $updateLog : new UpdateLog();

        try {
            // json complete check
            foreach (array('links', 'meta', 'nodes') as $key) {
                if (!array_key_exists($key, $data) || !is_array($data[$key])) {
                    throw new JsonImporterException($key);
                }
            }

            // do the import
            $this->updateFileInfo($data['meta'], $log);
            $this->importNodes($data['nodes'], $log);
            $this->importLinks($data['links'], $log);

        } catch (JsonImporterException $e) {
            $log->addMessage('essention part ' . $e->getWrongKey() . ' of the json is missing');
        }
        $this->em->persist($log);
        $this->em->flush();
        return $log;
    }

    /**
     * @param array $data
     * @param UpdateLog $log
     * @throws JsonImporterException
     */
    private function updateFileInfo(array &$data, UpdateLog $log)
    {
        if (!array_key_exists('timestamp', $data)) {
            throw new JsonImporterException('meta.timestamp');
        }
        $log->setFileTime(new \DateTime($data['timestamp']));
    }

    ///////////
    // NODES //
    ///////////

    /**
     * @param array $data
     * @param UpdateLog $log
     */
    private function importNodes(array &$data, UpdateLog $log)
    {
        // keep a list of all node id's for the safety removal later
        $existing = array();

        // iterate all nodes in the file
        foreach ($data as $key => $node) {
            try {
                $node = $this->importNode($node, $log);
                $existing[] = $node;
            } catch (JsonImporterException $e) {
                $log->addMessage(
                    'error in node ' . $key . ' with property '
                    . $e->getWrongKey() . ' (' . $e->getMessage() . ')'
                );
            }
        }
        $this->em->flush();

        // remove all nodes which are not in our list
        $qb = $this->nodeRep->createQueryBuilder('n');
        if (!empty($existing)) {
            $qb->andWhere($qb->expr()->notIn('n.id', $this->createIdList($existing)));
        }
        foreach ($qb->getQuery()->getResult() as $node) {
            $this->em->remove($node);
            $log->nodeRemoved();
        }
        $this->em->flush();
    }

    /**
     * @param array $data
     * @param UpdateLog $log
     * @return Node
     */
    private function importNode(array &$data, UpdateLog $log)
    {
        $this->testData($data, array('flags', 'geo', 'id', 'macs', 'name'));

        // import the node
        $node = new Node();
        $node->setMac($data['id']);
        if ($data['geo'] != null) {
            $node->setLatitude($data['geo'][0]);
            $node->setLongitude($data['geo'][1]);
        }
        $node->setNodeName($data['name'] != '' ? $data['name'] : null);

        // check if valid and add it if needed
        $violations = $this->validator->validate($node);
        $this->handleViolations($violations);

        $existingNode = $this->nodeRep->findByMac($node->getMac());
        if ($existingNode != null) {
            $log->nodePreserved();
        } else {
            $this->em->persist($node);
            $log->nodeAdded();
            $existingNode = $node;
        }

        // now add the node status to the existing node
        $status = new NodeStat();
        $status->setNode($existingNode);
        $status->setOnline((bool)$data['flags']['online']);
        $status->setClientCount(count(explode(', ', $data['macs'])));

        // look for the newest status of the node
        $lastStatus = $this->nodeStatRep->getLastStatOf($existingNode);
        if ($lastStatus == null || !$lastStatus->equals($status)) {
            $existingNode->addStat($status);
            $this->em->persist($status);
            $log->statusUpdated();
        }

        return $existingNode;
    }

    ///////////
    // LINKS //
    ///////////

    /**
     * @param array $data
     * @param UpdateLog $log
     */
    private function importLinks(array &$data, UpdateLog $log)
    {
        // keep a list of all link id's for the update
        $existing = array();

        // add all links
        foreach ($data as $key => $link) {
            try {
                $link = $this->importLink($link, $log);
                $existing[] = $link;
            } catch (JsonImporterException $e) {
                $log->addMessage(
                    'error in link ' . $key . ' with property '
                    . $e->getWrongKey() . ' (' . $e->getMessage() . ')'
                );
            }
        }
        $this->em->flush();

        // also set all links closed if they were not listed
        $qb = $this->linkRep->createQueryBuilder('l')->update();
        $qb->set('l.closedAt', $qb->expr()->literal(date('Y-m-d H:i:s')));
        if (!empty($existing)) {
            $qb->andWhere($qb->expr()->notIn('l.id', $this->createIdList($existing)));
        }
        $qb->andWhere($qb->expr()->isNull('l.closedAt'));
        $removed = $qb->getQuery()->execute();
        $log->setLinksRemoved($removed);
    }

    /**
     * @param array $data
     * @param UpdateLog $log
     * @return Link
     */
    private function importLink(array &$data, UpdateLog $log)
    {
        $this->testData($data, array('id', 'quality', 'source', 'target', 'type'));

        // create the links
        $link = new Link();
        list($target, $source) = explode('-', $data['id']);
        $link->setTarget($this->nodeRep->findByMac($target));
        $link->setSource($this->nodeRep->findByMac($source));
        $link->setType($data['type'] == 'client' ? Link::CLIENT : Link::VPN);
        $link->setQuality($data['quality']);

        // check if valid and add it if needed
        $violations = $this->validator->validate($link);
        $this->handleViolations($violations);

        $existingLink = $this->linkRep->findExistingLink($link);
        if ($existingLink != null) {
            $log->linkPreserved();
        } else {
            $this->em->persist($link);
            $log->linkAdded();
            $existingLink = $link;
        }

        return $existingLink;
    }

    ///////////
    // UTILS //
    ///////////

    /**
     * @param array $data
     * @param array $keys
     * @throws JsonImporterException
     */
    private static function testData(array &$data, array $keys)
    {
        // check if there is everything
        foreach ($keys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new JsonImporterException($key);
            }
        }
    }

    /**
     * @param ConstraintViolationListInterface $violations
     * @throws JsonImporterException
     */
    private static function handleViolations(ConstraintViolationListInterface $violations)
    {
        if ($violations->count() > 0) {
            $violation = $violations->get(0);
            throw new JsonImporterException($violation->getPropertyPath(), $violation->getMessage());
        }
    }

    private static function createIdList(array $entities)
    {
        $list = array();
        foreach ($entities as $entity) {
            $list[] = $entity->getId();
        }
        return $list;
    }
}