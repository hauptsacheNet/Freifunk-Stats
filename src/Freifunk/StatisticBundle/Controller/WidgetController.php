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
     * Example request: `/test/div?node=<nodeName>
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
        $nodeRepository = $manager->getRepository(
            'FreifunkStatisticBundle:Node'
        );
        $statRepository = $manager->getRepository(
            'FreifunkStatisticBundle:NodeStat'
        );

        $node = $nodeRepository->findByNodeName(
            $request->query->get('node')
        );

        if ($node) {
            $stats = $statRepository->getLastStatOf($node);
            $clients = $stats->getClientCount();

            return array(
                'node' => $node,
                'clients' => $clients,
                'append_id' => $id
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
        $nodeRepository = $manager->getRepository(
            'FreifunkStatisticBundle:Node'
        );
        $linkRepository = $manager->getRepository(
            'FreifunkStatisticBundle:Link'
        );

        $series = array();

        $nodes = $nodeRepository->findByNodeName(
            $request->query->get('node')
        );

        foreach ($nodes as $node) {

            if ($node) {
                $stats = array();
                $now = new \DateTime("-22 hour");
                $now->setTime($now->format('H'), 0, 0);
                $last = new \DateTime("-23 hour");
                $last->setTime($last->format('H'), 0, 0);

                foreach (range(1, 24) as $h) {
                    $stats[] = array(
                        $now->getTimestamp() * 1000,
                        $linkRepository->countLinksForNodeBetween($node, $last, $now)
                    );
                    $now->modify("+1 hour");
                    $last->modify("+1 hour");
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
        $ob->chart->renderTo($id . '_chart');
        $ob->chart->type('line');

        $ob->title->text(null);
        $ob->xAxis->title(array('text'  => "Zeitspanne"));
        $ob->xAxis->type('datetime');
        $ob->yAxis->title(array('text'  => "Anzahl von Clients"));

        $ob->series($series);

        return array(
            'nodes' => $nodes,
            'chart' => $ob,
            'append_id' => $id
        );
    }
}