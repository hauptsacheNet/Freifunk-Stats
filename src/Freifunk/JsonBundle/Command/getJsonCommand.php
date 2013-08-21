<?php
/**
 * This is the getJsonCommand
 *
 * This command downloads the `.json` file from the remote server.
 * The `.json` file is the base for all further statistic analysis.
 * You should run this command as a cron job every minute.
 * Add the `--dir=/path/` option to specify a directory where you want to store the downloaded files.
 *
 * PHP Version 5
 *
 * @category Test
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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Class getJsonCommand
 *
 * @category Test
 * @package  Freifunk\JsonBundle\Command
 * @author   Frederik Schubert <frederik@ferdynator.de>
 */
class getJsonCommand extends ContainerAwareCommand
{

    /**
     * @const string The URL where the nodes.json can be found.
     */
    const JSON_URL = 'http://graph.hamburg.freifunk.net/nodes.json';

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

        $dir = $input->getOption('dir');
        $fs = new Filesystem();

        // get last modified:
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::JSON_URL);
        curl_setopt($curl, CURLOPT_FILETIME, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($fs->exists($dir . 'latest')) {
            curl_setopt($curl, CURLOPT_TIMECONDITION, CURL_TIMECOND_IFMODSINCE);
            curl_setopt($curl, CURLOPT_TIMEVALUE, file_get_contents($dir . 'latest'));
        }

        $header = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        $filename = $dir. $info['filetime'].'.json';

        if ($info['http_code'] == 300 || $fs->exists($filename)) {
            $output->writeln('This json is saved already.');
            return;
        }

        // download newest and save it:
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::JSON_URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);

        try {

            $fs->dumpFile($filename, $data);

            // save last timestamp in extra file:
            $fs->dumpFile($dir . 'latest', $info['filetime']);

            $output->writeln('Json file saved to '. $dir . $info['filetime'].'.json');
        } catch (IOException $e) {
            $output->writeln('Could not write the file. Do you have permissions?');
        }
    }

}