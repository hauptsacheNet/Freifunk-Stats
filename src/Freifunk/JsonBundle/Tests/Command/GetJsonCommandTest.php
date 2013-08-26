<?php
/**
 * This is the GetJsonCommandTest
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

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Freifunk\JsonBundle\Command\GetJsonCommand;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;


/**
 * Class GetJsonCommandTest
 *
 * @category Test
 * @package  Freifunk\JsonBundle\Tests
 * @author   Frederik Schubert <frederik@ferdynator.de>
 */
class GetJsonCommandTest extends WebTestCase
{

    /**
     * This function tests the basic `execute` method of the command.
     *
     * @return null none
     */
    public function testExecute()
    {

        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new GetJsonCommand());

        $command = $application->find('freifunk:get-json');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName(), '--dir' => '/tmp/')
        );

        $this->assertRegExp('/Json file saved./', $commandTester->getDisplay());

        // cleanup
        $fs = new Filesystem();
        if ($fs->exists('/tmp/latest')) {
            $fs->remove('/tmp/latest');
        }

        unset($fs, $application, $kernel, $command, $commandTester);

    }

}
