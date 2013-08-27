<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marco
 * Date: 26.08.13
 * Time: 15:45
 * To change this template use File | Settings | File Templates.
 */

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Freifunk\StatisticBundle\Command\ImportJsonCommand;

class ImportJsonCommandTest extends WebTestCase
{
    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $em;

    protected function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        // build database
        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->createSchema($metadatas);
    }

    public static function validTestFiles()
    {
        return array(
            array(__DIR__."/TestFiles/document.json")
        );
    }

    /**
     * @dataProvider validTestFiles
     */
    public function testNormalParse($file)
    {
        $application = new Application(static::$kernel);
        $application->add(new ImportJsonCommand());

        $command = $application->find('freifunk:import-json');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName(), '--file' => $file)
        );

        $result  = $commandTester->getDisplay();
        $this->assertRegExp('/nodes\\(new: 10, preserved: 0, removed: 0\\)\\nlinks\\(new: 1, preserved: 0, removed: 0\\)$/', $result);
    }
}