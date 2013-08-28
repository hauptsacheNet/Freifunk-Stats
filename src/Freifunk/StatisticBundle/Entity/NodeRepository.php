<?php

namespace Freifunk\StatisticBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * NodeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NodeRepository extends EntityRepository
{

    /**
     * @param $mac
     * @return Node
     */
    public function findByMac($mac)
    {
        $qb = $this->createQueryBuilder("n");
        $qb->andWhere($qb->expr()->eq("n.mac", $qb->expr()->literal($mac)));
        return $qb->getQuery()->getOneOrNullResult();
    }
    /**
     * Counts all nodes in the database.
     *
     * @return mixed Number of nodes
     */
    public function countAllNodes()
    {
        $manager = $this->getEntityManager();
        $query = $manager->createQuery('SELECT COUNT(n.id) FROM Freifunk\\StatisticBundle\\Entity\\Node n');
        $count = $query->getSingleScalarResult();

        return $count;
    }

}
