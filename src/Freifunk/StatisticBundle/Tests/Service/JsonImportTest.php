<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marco
 * Date: 26.08.13
 * Time: 16:33
 * To change this template use File | Settings | File Templates.
 */

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JsonImportTest extends WebTestCase
{
    protected static $container;
    /** @var \Doctrine\ORM\EntityManager */
    protected static $em;

    public function setUp()
    {
        self::$kernel = static::createKernel();
        self::$kernel->boot();
        static::$container = self::$kernel->getContainer();
        static::$em = static::$container->get('doctrine.orm.entity_manager');
    }

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
     * @return \Freifunk\StatisticBundle\Service\JsonImporter
     */
    protected function getImporter () {
        return new \Freifunk\StatisticBundle\Service\JsonImporter(static::$em, static::$container->get("validator"));
    }

    /**
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
     * @dataProvider invalidData
     */
    public function testInvalidJSON($json)
    {
        $log = $this->getImporter()->fromString($json);
        $this->assertTrue($log->getMessage() != "");
    }

}
