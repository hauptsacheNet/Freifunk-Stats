<?php

namespace Freifunk\StatisticBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class WidgetControllerTest
 *
 * @package Freifunk\StatisticBundle\Tests\Controller
 */
class WidgetControllerTest extends WebTestCase
{
    /**
     * Tests if there are no nodes given on a widget call.
     */
    public function testNoNodeForWidget()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/widget/test');

        //$this->assertTrue($crawler->getInfo());
        $this->assertTrue($crawler->filter('html:contains("Keine Knoten angegeben.")')->count() > 0);
    }
}
