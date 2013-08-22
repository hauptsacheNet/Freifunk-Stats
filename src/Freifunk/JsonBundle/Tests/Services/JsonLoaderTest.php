<?php
/**
 * This is the getJsonCommandTest
 *
 * It contains some basics tests to test the
 * freifunk:get-json command
 *
 * PHP Version 5
 *
 * @category Test
 * @package  Freifunk\JsonBundle\Tests
 * @author   Frederik Schubert <frederik@ferdynator.de>
 *
 */

namespace Freifunk\JsonBundle\Tests\Services;

use Freifunk\JsonBundle\Services\JsonLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class GetJsonCommandTest
 *
 * @category Test
 * @package  Freifunk\JsonBundle\Tests
 * @author   Frederik Schubert <frederik@ferdynator.de>
*/
class JsonLoaderTest extends WebTestCase
{

    public function testCheckHeader()
    {

        $client = static::createClient();
        $jsonLoader = new JsonLoader($client->getContainer()->getParameter('json_url'));

        $this->assertTrue($jsonLoader->checkHeader('/tmp/'));
    }

}