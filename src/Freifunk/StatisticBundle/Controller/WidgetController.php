<?php

namespace Freifunk\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @package Freifunk\StatisticBundle\Controller
 */
class WidgetController extends Controller
{
    /**
     * First Widget
     *
     * @Route("/widget")
     * @Template()
     */
    public function indexAction(Request $request)
    {

        $params = $request->query->all();

        if (!$params['node']) {
            return array();
        }

        $manager = $this->getDoctrine()->getManager();
        $node_repository = $manager->getRepository('FreifunkStatisticBundle:Node');
        $stat_repository = $manager->getRepository('FreifunkStatisticBundle:NodeStat');

        $nodes = (is_string($params['node'])) ? array($params['node']) : $params['node'];

        $clients = 0;
        foreach ($nodes as $mac) {

            $node = $node_repository->findByMac($mac);

            if ($node) {
                $stats = $stat_repository->getLastStatOf($node);
                $clients += $stats->getClientCount();
            }

        }

        return array(
            'nodes' => $nodes,
            'clients' => $clients
        );
    }
}