<?php

namespace Freifunk\StatisticBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * NodeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NodeRepository extends EntityRepository
{

    /**
     * @param string $mac
     *
     * @return Node
     */
    public function findByMac($mac)
    {
        $qb = $this->createQueryBuilder("n");
        $qb->andWhere($qb->expr()->eq("n.mac", $qb->expr()->literal($mac)));

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Finds one or multiple nodes by their name(s)
     *
     * @param mixed $node
     *
     * @return Node[]
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function findByNodeName($node)
    {
        if (is_null($node) || empty($node)) {
            throw new NotFoundHttpException('Keine Knoten angegeben.');
        }

        $nodes = (!is_array($node)) ? array($node) : $node;

        $qb = $this->createQueryBuilder('n');
        $qb->andWhere($qb->expr()->in('n.nodeName', $nodes));

        /** @var Node $node */
        $nodes = $qb->getQuery()->getResult();
        $result = array();
        foreach ($nodes as $node) {
            $result[$node->getNodeName()] = $node;
        }

        return $result;
    }

    /**
     * Counts all nodes in the database.
     *
     * @return mixed Number of nodes
     */
    public function countAllNodes()
    {
        $manager = $this->getEntityManager();
        $query = $manager->createQuery('SELECT COUNT(n.id) FROM FreifunkStatisticBundle:Node n WHERE n.nodeName IS NOT NULL');
        $count = $query->getSingleScalarResult();

        return $count;
    }

    /**
     * Returns a node that is a duplicate to the give one but with a changed mac
     *
     * @param string $nodeName Name of the node
     * @param array  $macs     Array of MACs this node might have.
     *
     * @return mixed
     */
    public function findDuplicateNodes($nodeName, $macs)
    {
        $hashedMacs = array_map(function($value) {
            return sha1(strtoupper($value));
        }, $macs);

        $qb = $this->createQueryBuilder('n');
        $qb->andWhere($qb->expr()->eq('n.nodeName', '?1'));
        $qb->andWhere($qb->expr()->in(
            'n.mac', $hashedMacs
        ));

        $qb->setParameter(1, $nodeName);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
