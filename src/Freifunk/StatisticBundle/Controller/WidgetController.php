<?php

namespace Freifunk\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Ob\HighchartsBundle\Highcharts\Highchart;

/**
 * Class DefaultController
 * @package Freifunk\StatisticBundle\Controller
 *
 * @Route("/widget")
 */
class WidgetController extends Controller
{

    /** @var array */
    public $nodes;

    /**
     * First Widget, very basic. Just displays the number of clients per node.
     * Example request: `/test?node=<mac-address>`
     *
     * @return array
     *
     * @Route("/test")
     * @Template()
     */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $node_repository = $manager->getRepository('FreifunkStatisticBundle:Node');
        $stat_repository = $manager->getRepository('FreifunkStatisticBundle:NodeStat');

        $clients = 0;
        foreach ($this->nodes as $mac) {

            $node = $node_repository->findByMac($mac);

            if ($node) {
                $stats = $stat_repository->getLastStatOf($node);
                $clients += $stats->getClientCount();
            }

        }

        return array(
            'nodes' => $this->nodes,
            'clients' => $clients
        );
    }

    /**
     * Returns a nice graph that displays all Clients for
     * the nodes over the selected period of time.
     *
     * @param string  $period_of_time
     *
     * @return array
     *
     * @Route("/clients/{period_of_time}")
     * @Template()
     */
    public function clientsPerTimeAction($period_of_time)
    {

        $manager = $this->getDoctrine()->getManager();
        $node_repository = $manager->getRepository('FreifunkStatisticBundle:Node');
        $link_repository = $manager->getRepository('FreifunkStatisticBundle:Link');

        $series = array();
        $now = new \DateTime();
        foreach ($this->nodes as $mac) {

            $node = $node_repository->findByMac($mac);

            if ($node) {
                $stats = $link_repository->countLinksForNodeBetween($node, $now, $now->modify('-1 day'));

                if (!$stats)
                    continue;

                $series[] = array(
                    'name' => $mac,
                    'data' => array($stats[1])
                );
            }
        }

        $ob = new Highchart();
        $ob->chart->renderTo('linechart');
        $ob->chart->type('column');

        $ob->title->text('Clients pro Knoten');

        $ob->xAxis->title(array('text'  => "Zeitspanne"));
        $ob->xAxis->categories(range(1, 24));

        $ob->yAxis->title(array('text'  => "Anzahl von Clients"));

        $ob->series($series);

        return array(
            'chart' => $ob
        );
    }
}