<?php

namespace Freifunk\StatisticBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 *
 * @package Freifunk\StatisticBundle\Tests\Controller
 */
class DefaultControllerTest extends WebTestCase
{
    /**
     * tests the basic statistic action
     */
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue(
            $crawler->filter(
                'html:contains("Knoten eingetragen")'
            )->count() > 0
        );
    }
}
