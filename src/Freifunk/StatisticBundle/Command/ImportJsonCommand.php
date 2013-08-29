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

/**
 * Class ImportJsonCommand
 *
 * @package Freifunk\StatisticBundle\Command
 */
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
            ->setDescription('Reads a .json file into the database')
            ->addOption(
                'file',
                'f',
                InputOption::VALUE_REQUIRED,
                'The json file to be read'
            )
            ->addOption(
                'remove',
                'r',
                InputOption::VALUE_NONE,
                'If the file should be removed afterwards'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->jsonParser = $this->getContainer()
            ->get('freifunk_statistic.json_importer');

        $file = $input->getOption("file");
        $this->useFile($file, $output, $input->getOption('remove'));
    }

    private function useFile($file, OutputInterface $output, $remove)
    {
        if (is_dir($file)) {
            $entries = scandir($file);
            sort($entries);
            foreach ($entries as $entry) {
                if (!preg_match('/^\.{1,2}$/', $entry)) {
                    $this->useFile($file . $entry, $output, $remove);
                }
            }
        } else if (is_file($file)) {
            $output->writeln('now parsing ' . $file);
            $log = $this->jsonParser->fromResource($file);
            $output->write($log->getMessage());
            $output->writeln($log->__toString());
            if ($remove) {
                unlink($file);
            }
        }
    }
}