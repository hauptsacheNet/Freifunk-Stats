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
use Assetic\Factory\Resource\ResourceInterface;
use Doctrine\ORM\EntityManager;
use Freifunk\StatisticBundle\Entity\Link;
use Freifunk\StatisticBundle\Entity\LinkRepository;
use Freifunk\StatisticBundle\Entity\Node;
use Freifunk\StatisticBundle\Entity\NodeRepository;
use Freifunk\StatisticBundle\Entity\NodeStat;
use Freifunk\StatisticBundle\Entity\NodeStatRepository;
use Freifunk\StatisticBundle\Entity\UpdateLog;
use Freifunk\StatisticBundle\Importer\Import;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator;

class JsonImporter
{
    /** @var EntityManager */
    private $em;
    /** @var Validator */
    private $validator;

    /**
     * @param EntityManager $em
     * @param Validator $validator
     */
    public function __construct(EntityManager $em, Validator $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * @param string $resource
     * @return UpdateLog
     */
    public function fromResource($resource)
    {
        $import = new Import($this->em, $this->validator, $resource);
        return $import->execute();
    }
}