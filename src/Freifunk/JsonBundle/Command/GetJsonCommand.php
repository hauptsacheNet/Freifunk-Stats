<?php
/**
 * This is the GetJsonCommand
 *
 * This command downloads the `.json` file from the remote server.
 * The `.json` file is the base for all further statistic analysis.
 * You should run this command as a cron job every minute.
 * Add the `--dir=/path/` option to specify a directory where you want to store the downloaded files.
 *
 * PHP Version 5
 *
 * @category Command
 * @package  Freifunk\JsonBundle\Command
 * @author   Frederik Schubert <frederik@ferdynator.de>
 *
 */

namespace Freifunk\JsonBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Class GetJsonCommand
 *
 * @category Command
 * @package  Freifunk\JsonBundle\Command
 * @author   Frederik Schubert <frederik@ferdynator.de>
 */
class GetJsonCommand extends ContainerAwareCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('freifunk:get-json')
            ->setDescription('Downloads the newest .json file from the servers.')
            ->addOption('dir', null, InputOption::VALUE_OPTIONAL, 'The path where the .json should be saved.', '');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $jsonLoader = $this->getContainer()->get('freifunk_json.json_loader');
        $dir = $input->getOption('dir');

        if (!$jsonLoader->checkHeader($dir)) {
            $output->writeln('This json is saved already.');

            return;
        }

        try {
            $name = $jsonLoader->saveJson($dir);
            $output->writeln('Json file saved to '.$name);
        } catch (IOException $e) {
            $output->writeln('Could not write the file. Do you have permissions?');
        }
    }

}