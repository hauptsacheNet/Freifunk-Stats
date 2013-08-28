<?php

namespace Freifunk\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
    public function indexAction()
    {


        return array();
    }
}