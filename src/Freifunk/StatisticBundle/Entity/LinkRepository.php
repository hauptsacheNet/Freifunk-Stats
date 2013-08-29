<?php

namespace Freifunk\StatisticBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Freifunk\StatisticBundle\Entity\Node;
use Freifunk\StatisticBundle\Entity\Link;

/**
 * LinkRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LinkRepository extends EntityRepository
{
    /**
     * @param Link $link
     *
     * @return Link
     */
    public function findExistingLink(Link $link)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->andWhere($qb->expr()->eq('l.source', $qb->expr()->literal($link->getSource()->getId())));
        $qb->andWhere($qb->expr()->eq('l.target', $qb->expr()->literal($link->getTarget()->getId())));
        $qb->andWhere($qb->expr()->eq('l.type', $qb->expr()->literal($link->getType())));
        $qb->andWhere($qb->expr()->isNull('l.closeTime'));

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Returns the number of links for a node between 2 dates
     *
     * @param Node      $node
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return mixed
     */
    public function countLinksForNodeBetween(Node $node, \DateTime $start, \DateTime $end)
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT COUNT(l.source)
                FROM FreifunkStatisticBundle:Link l
                WHERE
                    l.source = ?1
                    AND l.openTime <= ?2
                    AND (l.closeTime >= ?3 OR l.closeTime IS NULL)
                    AND l.type = ?4')
            ->setParameters(array(
                1 => $node->getId(),
                2 => $end,
                3 => $start,
                4 => Link::CLIENT
            ));

        return (int) $query->getSingleScalarResult();
    }
}
