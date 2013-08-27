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
use Freifunk\StatisticBundle\Entity\NodeStatRepository;
use Freifunk\StatisticBundle\Entity\UpdateLog;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator;

class JsonImporter
{
    /** @var Container */
    private $container;
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
        if ($data === null) {
            $log = new UpdateLog();
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
     * @return UpdateLog
     */
    public function fromData(array $data)
    {
        $log = new UpdateLog();

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

    private function updateFileInfo(array $data, UpdateLog $log)
    {
        if (!array_key_exists('timestamp', $data)) {
            throw new JsonImporterException('meta.timestamp');
        }
        $log->setFileTime(new \DateTime($data['timestamp']));
    }

    ///////////
    // NODES //
    ///////////

    private function importNodes(array $data, UpdateLog $log)
    {
        $lastFlush = -1;
        foreach ($data as $key => $node) {
            // on 100 new nodes flush once
            if ($log->getNodesAdded() % 100 == 0 && $log->getNodesAdded() != $lastFlush) {
                $this->em->flush();
                $lastFlush = $log->getNodesAdded();
            }
            // try to import the node
            try {
                $this->importNode($node, $log);
            } catch (JsonImporterException $e) {
                $log->addMessage(
                    'error in node ' . $key . ' with the property '
                    . $e->getWrongKey() . ' with value ' . $node[$e->getWrongKey()]
                    . ' (' . $e->getMessage() . ')'
                );
            }
        }
    }

    private function importNode(array $data, UpdateLog $log)
    {
        // check if we have everything
        foreach (array('flags', 'geo', 'id', 'macs', 'name') as $key) {
            if (!array_key_exists($key, $data)) {
                throw new JsonImporterException($key);
            }
        }

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
        if ($violations->count()) {
            /** @var ConstraintViolationInterface $violation */
            foreach ($violations->getIterator() as $violation) {
                throw new JsonImporterException($violation->getPropertyPath(), $violation->getMessage());
            }
        } else {
            if ($this->nodeRep->findByMac($node->getMac()) != null) {
                $log->nodePreserved();
            } else {
                $this->em->persist($node);
                $log->nodeAdded();
            }
        }
    }

    ///////////
    // LINKS //
    ///////////

    private function importLinks(array $data, UpdateLog $log)
    {
        $lastFlush = -1;
        foreach ($data as $key => $link) {
            // on 100 new links flush once
            if ($log->getLinksAdded() % 100 == 0 && $log->getLinksAdded() != $lastFlush) {
                $this->em->flush();
                $lastFlush = $log->getLinksAdded();
            }
            // import a link
            try {
                $this->importLink($link, $log);
            } catch (JsonImporterException $e) {
                $log->addMessage(
                    'error in link ' . $key . '. The property '
                    . $e->getWrongKey() . ' with value ' . $link[$e->getWrongKey()]
                    . ' (' . $e->getMessage() . ')'
                );
            }
        }
    }

    private function importLink(array $data, UpdateLog $log)
    {
        // check if there is everything
        foreach (array('id', 'quality', 'source', 'target', 'type') as $key) {
            if (!array_key_exists($key, $data)) {
                throw new JsonImporterException($key);
            }
        }

        // create the links
        $link = new Link();
        list($target, $source) = explode('-', $data['id']);
        $link->setTarget($this->nodeRep->findByMac($target));
        $link->setSource($this->nodeRep->findByMac($source));
        $link->setType($data['type'] == 'client' ? Link::CLIENT : Link::VPN);
        $link->setQuality($data['quality']);

        // check if valid and add it if needed
        $violations = $this->validator->validate($link);
        if ($violations->count()) {
            /** @var ConstraintViolationInterface $violation */
            foreach ($violations->getIterator() as $violation) {
                throw new JsonImporterException($violation->getPropertyPath(), $violation->getMessage());
            }
        } else {
            $existing = $this->linkRep->findExistingLink($link);
            if ($existing != null) {
                $log->linkPreserved();
            } else {
                $this->em->persist($link);
                $log->linkAdded();
            }
        }
    }
}