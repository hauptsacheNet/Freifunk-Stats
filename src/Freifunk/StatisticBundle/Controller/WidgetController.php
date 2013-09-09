<?php

namespace Freifunk\StatisticBundle\Controller;

use Freifunk\StatisticBundle\Entity\LinkRepository;
use Freifunk\StatisticBundle\Entity\NodeRepository;
use Freifunk\StatisticBundle\Entity\NodeStatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Ob\HighchartsBundle\Highcharts\Highchart;

/**
 * Class DefaultController
 *
 * @package Freifunk\StatisticBundle\Controller
 *
 */
class WidgetController extends Controller
{
    /** @var NodeRepository */
    private $nodeRepository;
    /** @var NodeStatRepository */
    private $statRepository;
    /** @var LinkRepository */
    private $linkRepository;

    /**
     * {@inheritDocs}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $manager = $this->getDoctrine()->getManager();
        $this->nodeRepository = $manager->getRepository(
            'FreifunkStatisticBundle:Node'
        );
        $this->statRepository = $manager->getRepository(
            'FreifunkStatisticBundle:NodeStat'
        );
        $this->linkRepository = $manager->getRepository(
            'FreifunkStatisticBundle:Link'
        );
    }

    /**
     * Generates a basic hightchart
     *
     * @param string $xTitle Title of the x axis
     * @param string $yTitle Title of the y axis
     * @param string $accent Colors
     * @param string $text   Text colors
     *
     * @return Hightchart
     */
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
     * @param string  $id
     *
     * @return array
     *
     * @Route("/widget/test/{id}")
     * @Template()
     */
    public function indexAction(Request $request, $id)
    {

        $node = $this->nodeRepository->findByNodeName(
            $request->query->get('node')
        );

        if (!$node) {
            throw $this->createNotFoundException('Knoten nicht gefunden');
        }

        $stats = $this->statRepository->getLastStatOf($node);
        $clients = $stats->getClientCount();

        return array(
            'node' => $node,
            'clients' => $clients,
            'append_id' => $id
        );
    }

    /**
     * Creates a hotlink to all nodes in the db for deep-linking
     *
     * @param Request $request
     * @param string  $nodeName
     *
     * @return array
     *
     * @Route("/{nodeName}")
     * @Template()
     */
    public function nodeHotlinkAction(Request $request, $nodeName)
    {

        $node = $this->nodeRepository->findOneBy(array(
            'nodeName' => $nodeName
        ));

        if (!$node) {
            throw $this->createNotFoundException('Knoten nicht gefunden');
        }

        return array(
            'node' => $node
        );
    }

    /**
     * Returns a nice graph that displays all Clients for
     * the nodes over the selected period of time.
     *
     * @param Request $request
     * @param string  $id
     *
     * @return array
     *
     * @Route("/widget/clients/{id}")
     * @Template()
     */
    public function clientsPerHourAction(Request $request, $id)
    {
        $nodeNames = array();
        $nodeQuery = $request->query->get('node');
        foreach ($nodeQuery as $row) {
            foreach ($row['nodes'] as $node) {
                $nodeNames[] = $node;
            }
        }

        $series = array();
        $nodes = $this->nodeRepository->findByNodeName($nodeNames);

        foreach ($nodeQuery as $row) {
            $nodeBundle = array();
            foreach ($row['nodes'] as $nodeName) {
                if (array_key_exists($nodeName, $nodes)) {
                    $nodeBundle[] = $nodes[$nodeName];
                } else {
                    throw $this->createNotFoundException('node ' . $nodeName . ' not found');
                }
            }

            // create timeline
            $timeline = $this->linkRepository->computeLinkTimeline($nodeBundle);
            $nodeStats = array();
            foreach ($timeline as $date => $count) {
                $nodeStats[] = array(strtotime($date) * 1000, $count);
            }

            $series[] = array(
                'name' => $row['name'] ? $row['name'] : $nodeBundle[0]->getNodeName(),
                'data' => $nodeStats
            );
        }

        $ob = $this->createHighChart(null, 'Anzahl Clients');
        $ob->chart->renderTo($id . '_chart');
        $ob->chart->type('line');
        $ob->chart->spacingRight(130);
        $ob->chart->zoomType('x');
        $ob->xAxis->type('datetime');
        $ob->legend->align('right');
        $ob->legend->verticalAlign('top');
        $ob->legend->x(125);
        $ob->legend->y(30);
        $ob->legend->layout('vertical');
        $ob->legend->floating(true);
        $ob->legend->itemWidth(120);
        $ob->legend->borderWidth(0);

        $ob->series($series);

        return array(
            'nodes' => $nodes,
            'chart' => $ob,
            'append_id' => $id
        );
    }
}