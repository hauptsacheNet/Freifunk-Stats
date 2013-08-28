<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marco
 * Date: 26.08.13
 * Time: 15:42
 * To change this template use File | Settings | File Templates.
 */

namespace Freifunk\StatisticBundle\Command;


use Assetic\Factory\Resource\FileResource;
use Freifunk\StatisticBundle\Service\JsonImporter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportJsonCommand extends ContainerAwareCommand
{
    /** @var JsonImporter */
    private $jsonParser;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('freifunk:import-json')
            ->setDescription('puts the data of the specified file into the database')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'The json file to be read');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->jsonParser = $this->getContainer()->get('freifunk_statistic.json_importer');
        $file = $input->getOption("file");
        $this->useFile($file, $output);
    }

    private function useFile($file, OutputInterface $output)
    {
        if (is_dir($file)) {
            $entries = scandir($file);
            sort($entries);
            foreach ($entries as $entry) {
                if (!preg_match('/^\.{1,2}$/', $entry)) {
                    $this->useFile($file . $entry, $output);
                }
            }
        } else if (is_file($file)) {
            $output->writeln('now parsing ' . $file);
            $log = $this->jsonParser->fromResource($file);
            $output->write($log->getMessage());
            $output->writeln($log->__toString());
        }
    }
}