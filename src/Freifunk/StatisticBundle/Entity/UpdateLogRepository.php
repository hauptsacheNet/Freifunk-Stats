<?php

namespace Freifunk\StatisticBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UpdateLogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UpdateLogRepository extends EntityRepository
{
    /**
     * @param $timestamp
     * @return mixed
     */
    public function findLogsAfter($timestamp)
    {
        $manager = $this->getEntityManager();
        $query = $manager->createQuery('SELECT COUNT(l.id) FROM Freifunk\\StatisticBundle\\Entity\\UpdateLog l WHERE l.fileTime > ?1');
        $count = $query->setParameter(1, $timestamp)->getSingleScalarResult();

        return $count;
    }

    /**
     * @param $timestamp
     * @return mixed
     */
    public function findLogSizeAfter($timestamp)
    {
        $manager = $this->getEntityManager();
        $query = $manager->createQuery('SELECT SUM(l.fileSize) FROM Freifunk\\StatisticBundle\\Entity\\UpdateLog l WHERE l.fileTime > ?1');
        $count = $query->setParameter(1, $timestamp)->getSingleScalarResult();

        return $count;
    }

    /**
     * Gets the last log entry
     *
     * @return UpdateLog
     */
    public function getLastEntry()
    {
        $qb = $this->createQueryBuilder('l');
        $qb->orderBy('l.fileTime', 'DESC');
        $qb->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Gets the last log entry
     *
     * @return UpdateLog
     */
    public function getLastSuccessfulEntry()
    {
        $qb = $this->createQueryBuilder('l');
        $qb->andWhere($qb->expr()->isNotNull('l.fileTime'));
        $qb->orderBy('l.fileTime', 'DESC');
        $qb->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }
}
