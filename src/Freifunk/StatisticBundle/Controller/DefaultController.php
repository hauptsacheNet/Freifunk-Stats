<?php

namespace Freifunk\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class DefaultController
 * @package Freifunk\StatisticBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * Just some basic statistic generation
     *
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {

        $manager = $this->getDoctrine()->getManager();

        $nodeRepository = $manager->getRepository('FreifunkStatisticBundle:Node');
        $updateLogRepository = $manager->getRepository('FreifunkStatisticBundle:UpdateLog');

        //$size_query = $this->get('database_connection')->query('SELECT pg_size_pretty(pg_database_size(\'freifunk\'));')->fetchAll();

        $now = new \DateTime('now');

        return array(
            'total_nodes' => $nodeRepository->countAllNodes(),
            'logs' => $updateLogRepository->findAll(),
            'table_size' => 0, //$size_query[0]['pg_size_pretty'],
            'logs_per_hour' => $updateLogRepository->findLogsAfter($now->modify('-1 hour')),
            'logs_per_day' => $updateLogRepository->findLogsAfter($now->modify('+1 hour -1 day')),
            'logs_per_week' => $updateLogRepository->findLogsAfter($now->modify('-6 days')),
            'size_per_hour' => $updateLogRepository->findLogSizeAfter($now->modify('-1 hour')),
            'size_per_day' => $updateLogRepository->findLogSizeAfter($now->modify('+1 hour -1 day')),
            'size_per_week' => $updateLogRepository->findLogSizeAfter($now->modify('-6 days'))
        );
    }
}
