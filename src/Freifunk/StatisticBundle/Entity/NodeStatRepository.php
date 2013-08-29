<?php

namespace Freifunk\StatisticBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * NodeStatRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NodeStatRepository extends EntityRepository
{
    /**
     * Returns the last node status of the specified node
     *
     * @param Node $node
     *
     * @return NodeStat
     */
    public function getLastStatOf(Node $node)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->andWhere($qb->expr()->eq('s.node', $qb->expr()->literal($node->getId())));
        $qb->orderBy('s.time', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
