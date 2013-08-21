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

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Freifunk\JsonBundle\Command\getJsonCommand;


/**
 * Class getJsonCommandTest
 *
 * @category Test
 * @package  Freifunk\JsonBundle\Tests
 * @author   Frederik Schubert <frederik@ferdynator.de>
 */
class getJsonCommandTest extends \PHPUnit_Framework_TestCase
{

    /**
     * This function tests the basic `execute` method of the command.
     *
     * @return null none
     */
    public function testExecute()
    {

        $application = new Application();
        $application->add(new getJsonCommand());

        $command = $application->find('freifunk:get-json');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName(), '--dir' => '/tmp/')
        );

        $this->assertRegExp('/Json file saved to /', $commandTester->getDisplay());

    }

}
