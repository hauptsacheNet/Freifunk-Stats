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

    private function createHighChart($xTitle, $yTitle, $accent = '#E0266B', $text = '#000')
    {
        $chart = new Highchart();
        $chart->title->text(null);
        $chart->chart->backgroundColor('transparent');
        $chart->chart->style(array('color' => $accent));
        $chart->title->style(array('color' => $accent));
        $chart->xAxis->labels(array('style' => array('color' => $text)));
        $chart->xAxis->title(array('text' => $xTitle, 'style' => array('color' => $text)));
        $chart->xAxis->lineColor($accent);
        $chart->xAxis->tickColor($accent);
        $chart->yAxis->labels(array('style' => array('color' => $text)));
        $chart->yAxis->title(array('text' => $yTitle, 'style' => array('color' => $text)));
        $chart->yAxis->lineColor($accent);
        $chart->yAxis->tickColor($accent);
        $chart->colors = array(
            $accent,
            '#2f7ed8',
            '#0d233a',
            '#8bbc21',
            '#910000',
            '#1aadce',
            '#492970',
            '#f28f43',
            '#77a1e5',
            '#c42525',
            '#a6c96a'
        );
        return $chart;
    }

    /**
     * First Widget, very basic. Just displays the number of clients per node.
     * Example request: `/test/div?node=<nodeName>
     *
     * @param Request $request
     * @param string $id
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
     * @param Request $request
     * @param int $id
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

        $ob = $this->createHighChart(null, 'Anzahl Clients');
        $ob->chart->renderTo($id . '_chart');
        $ob->chart->type('line');
        $ob->chart->spacingRight(130);
        $ob->xAxis->type('datetime');
        $ob->legend->align('right');
        $ob->legend->verticalAlign('top');
        $ob->legend->x(125);
        $ob->legend->y(30);
        $ob->legend->layout('vertical');
        $ob->legend->floating(true);
        $ob->legend->itemWidth(120);
        $ob->legend->borderWidth(0);
        $ob->labels->style(array(
            'font-weight' => 'bold'
        ));

        $ob->series($series);

        return array(
            'nodes' => $nodes,
            'chart' => $ob,
            'append_id' => $id
        );
    }
}