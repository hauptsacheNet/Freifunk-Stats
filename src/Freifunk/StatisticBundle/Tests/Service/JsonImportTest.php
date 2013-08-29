<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marco
 * Date: 26.08.13
 * Time: 16:33
 * To change this template use File | Settings | File Templates.
 */

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Freifunk\StatisticBundle\Service\JsonImporter;

/**
 * Class JsonImportTest
 */
class JsonImportTest extends WebTestCase
{
    /** @var  Container */
    protected static $container;
    /** @var \Doctrine\ORM\EntityManager */
    protected static $em;

    /**
     * {@inheritDocs}
     */
    public function setUp()
    {
        self::$kernel = static::createKernel();
        self::$kernel->boot();
        static::$container = self::$kernel->getContainer();
        static::$em = static::$container->get('doctrine.orm.entity_manager');
    }

    /**
     * Provides invalid files.
     *
     * @return array
     */
    public static function invalidFileNames()
    {
        return array(
            array('/tmp'),
            array('/proc/meminfo'),
            array(null),
            array(false),
            array(31)
        );
    }

    /**
     * Provides invalid data.
     *
     * @return array
     */
    public static function invalidData()
    {
        return array(
            array('{}'),
            array('{"nodes":null, "link":null}'),
            array('{"meta":null, "link":null}'),
            array('{"nodes":null, "meta":null}'),
            array('{"link":null, "meta":null, "nodes":null}'),
        );
    }

    /**
     * Returns the Importer service.
     *
     * @return \Freifunk\StatisticBundle\Service\JsonImporter
     */
    protected function getImporter ()
    {
        return new JsonImporter(
            static::$em,
            static::$container->get("validator")
        );
    }

    /**
     * test invalid files:
     *
     * @param string $file
     *
     * @dataProvider invalidFileNames
     */
    public function testFromFileError($file)
    {
        $log = $this->getImporter()->fromResource($file);
        $this->assertEquals(0, $log->getNodesAdded());
        $this->assertEquals(0, $log->getNodesPreserved());
        $this->assertEquals(0, $log->getNodesRemoved());
        $this->assertEquals(0, $log->getLinksAdded());
        $this->assertEquals(0, $log->getLinksPreserved());
        $this->assertEquals(0, $log->getLinksRemoved());
    }

    /**
     * Tests invalid json
     *
     * @param string $json
     *
     * @dataProvider invalidData
     */
    public function testInvalidJSON($json)
    {
        $log = $this->getImporter()->fromString($json);
        $this->assertTrue($log->getMessage() != "");
    }

}
