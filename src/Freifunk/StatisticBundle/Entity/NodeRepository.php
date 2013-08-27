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
