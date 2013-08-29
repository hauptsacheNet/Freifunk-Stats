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
use Freifunk\StatisticBundle\Importer\ImportException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator;

/**
 * Class JsonImporter
 *
 * @package Freifunk\StatisticBundle\Service
 */
class JsonImporter
{
    /** @var EntityManager */
    private $em;
    /** @var Validator */
    private $validator;

    /**
     * @param EntityManager $em
     * @param Validator     $validator
     *
     * @return JsonImporter
     */
    public function __construct(EntityManager $em, Validator $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * Imports .json file from resource
     *
     * @param string $resource
     *
     * @return UpdateLog
     */
    public function fromResource($resource)
    {
        if (is_file($resource) && is_readable($resource)) {

            return $this->fromString(file_get_contents($resource));
        } else {
            $log = new UpdateLog();
            $log->addMessage('The given json is not readable');
            $log->finish();
            $this->em->persist($log);
            $this->em->flush();

            return $log;
        }
    }

    /**
     * Imports json directly from a string.
     *
     * @param string $string
     *
     * @return UpdateLog
     */
    public function fromString($string)
    {
        $import = new Import($this->em, $this->validator, $string);

        return $import->execute();
    }
}