<?php

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Freifunk\StatisticBundle\Command\ImportJsonCommand;

/**
 * Class ImportJsonCommandTest
 */
class ImportJsonCommandTest extends WebTestCase
{
    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $em;

    /**
     * {@inheritDocs}
     */
    public static function setUpBeforeClass()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        /** @var \Doctrine\Common\Persistence\ObjectManager $em */
        $em = static::$kernel->getContainer()
            ->get('doctrine.orm.entity_manager');

        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        if (!empty($metadatas)) {
            $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
            $tool->dropSchema($metadatas);
            $tool->createSchema($metadatas);
        }
        static::$kernel->shutdown();
    }

    /**
     * {@inheritDocs}
     */
    protected function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine.orm.entity_manager');
    }

    /**
     * Provides valid test files
     *
     * @return array
     */
    public static function validTestFiles()
    {
        return array(
            array(
                __DIR__ . "/TestFiles/document.json",
                array(10, 0, 0),
                array(1, 0, 0),
                10
            ),
            array(
                __DIR__ . "/TestFiles/document 2.json",
                array(0, 8, 2),
                array(0, 1, 0),
                0
            ),
            array(
                __DIR__ . "/TestFiles/document 3.json",
                array(0, 0, 8),
                array(0, 0, 0),
                0
            )
        );
    }

    /**
     * Tests normal parsing.
     *
     * @param string $file
     * @param array  $nodeUpdates
     * @param array  $linkUpdates
     * @param int    $statusUpdates
     *
     * @dataProvider validTestFiles
     */
    public function testNormalParse($file, $nodeUpdates, $linkUpdates, $statusUpdates)
    {
        $application = new Application(static::$kernel);
        $application->add(new ImportJsonCommand());

        $command = $application->find('freifunk:import-json');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName(), '--file' => $file)
        );

        $result = $commandTester->getDisplay();
        $this->assertRegExp(
            '/nodes\\(new: ' . $nodeUpdates[0] . ', preserved: '
                . $nodeUpdates[1] . ', removed: ' . $nodeUpdates[2] . '\\)'
                . '\\nlinks\\(new: ' . $linkUpdates[0] . ', preserved: '
                . $linkUpdates[1] . ', removed: ' . $linkUpdates[2] . '\\)'
                . '\\nalso there were ' . $statusUpdates . ' status updates$/',
            $result
        );

        $this->assertTrue(file_exists($file));
    }
}