<?php

namespace Freifunk\StatisticBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class JsControllerTest
 *
 * @package Freifunk\StatisticBundle\Tests\Controller
 */
class JsControllerTest extends WebTestCase
{

    /**
     * Test widget javascript generation
     */
    public function testJsGeneration()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/js/widget');

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
    }
}
