<?php

namespace Freifunk\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Ob\HighchartsBundle\Highcharts\Highchart;

/**
 * Class DefaultController
 *
 * @package Freifunk\StatisticBundle\Controller
 *
 * @Route("/widget")
 */
class WidgetController extends Controller
{

    /**
     * First Widget, very basic. Just displays the number of clients per node.
     * Example request: `/test?node=<mac-address>`
     *
     * @param Request  $request
     * @param string   $id
     *
     * @return array
     *
     * @Route("/test/{id}")
     * @Template()
     */
    public function indexAction(Request $request, $id)
    {
        $manager = $this->getDoctrine()->getManager();
        $nodeRepository = $manager->getRepository('FreifunkStatisticBundle:Node');
        $statRepository = $manager->getRepository('FreifunkStatisticBundle:NodeStat');

        $node = $nodeRepository->findOneBy(array(
            'nodeName' => $request->query->get('node')
        ));

        if ($node) {
            $stats = $statRepository->getLastStatOf($node);
            $clients = $stats->getClientCount();

            return array(
                'node' => $node,
                'clients' => $clients
            );
        }

        return $this->createNotFoundException('Knoten nicht gefunden');
    }

    /**
     * Returns a nice graph that displays all Clients for
     * the nodes over the selected period of time.
     *
     * @param Request  $request
     * @param int      $id
     *
     * @return array
     *
     * @Route("/clients/{id}")
     * @Template()
     */
    public function clientsPerHourAction(Request $request, $id)
    {

        $manager = $this->getDoctrine()->getManager();
        $nodeRepository = $manager->getRepository('FreifunkStatisticBundle:Node');
        $linkRepository = $manager->getRepository('FreifunkStatisticBundle:Link');

        $series = array();

        $nodes = $nodeRepository->findBy(array(
            'nodeName' => $request->query->get('node')
        ));

        foreach ($nodes as $node) {

            if ($node) {
                $stats = array();
                $now = new \DateTime();
                foreach (range(1, 24) as $h) {
                    $stats[] = $linkRepository->countLinksForNodeBetween($node, $now->modify('-1 hour'), $now->modify('+1 hour'));
                }

                if ($stats) {
                    $series[] = array(
                        'name' => $node->getNodeName(),
                        'data' => $stats
                    );
                }
            }
        }

        $ob = new Highchart();
        $ob->chart->renderTo($id);
        $ob->chart->type('column');

        $ob->title->text('Clients pro Knoten');

        $ob->xAxis->title(array('text'  => "Zeitspanne"));
        $ob->xAxis->categories(range(1, 24));

        $ob->yAxis->title(array('text'  => "Anzahl von Clients"));

        $ob->series($series);

        return array(
            'nodes' => $nodes,
            'chart' => $ob,
            'id' => $id
        );
    }
}