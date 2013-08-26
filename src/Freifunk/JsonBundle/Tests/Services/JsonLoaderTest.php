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
use Symfony\Component\Filesystem\Filesystem;

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

        $fs = new Filesystem();
        $fs->mkdir('/tmp/testCheckHeader/');

        $client = static::createClient();
        $jsonLoader = new JsonLoader($client->getContainer()->getParameter('json_url'));

        $this->assertTrue($jsonLoader->checkHeader('/tmp/testCheckHeader/'));

        $fs->remove('/tmp/testCheckHeader/');

        unset($fs, $client, $jsonLoader);
    }

}