<?php
/**
 * This is the GetJsonCommand
 *
 * This command downloads the `.json` file from the remote server.
 * The `.json` file is the base for all further statistic analysis.
 * You should run this command as a cron job every minute.
 * Add the `--dir=/path/` option to specify a directory where you
 * want to store the downloaded files.
 *
 * PHP Version 5
 *
 * @category Service
 * @package  Freifunk\JsonBundle\Service
 * @author   Frederik Schubert <frederik@ferdynator.de>
 *
 */

namespace Freifunk\JsonBundle\Services;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Class GetJsonCommand
 *
 * @category Service
 * @package  Freifunk\JsonBundle\Service
 * @author   Frederik Schubert <frederik@ferdynator.de>
 */
class JsonLoader
{

    /**
     * @var string Url
     */
    private $url;

    /**
     * @var Array cUrl info
     */
    private $info;

    /**
     * @var Filesystem Filesystem component
     */
    private $fs;

    /**
     * @param string $url Url where we get the json from.
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->fs = new Filesystem();
    }

    /**
     * Requests the headers of the `.json` file to check
     * whether or not we need to download it.
     *
     * @param string $dir The directory where to save the output
     * @param string $url The url of the Json file.
     *
     * @return boolean TRUE if the file is not saved yet. False otherwise.
     */
    public function checkHeader($dir, $url = null)
    {
        if (is_null($url)) {
            $url = $this->url;
        }

        // get last modified:
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FILETIME, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($this->fs->exists($dir . 'latest')) {
            curl_setopt(
                $curl,
                CURLOPT_TIMECONDITION,
                CURL_TIMECOND_IFMODSINCE
            );

            curl_setopt(
                $curl,
                CURLOPT_TIMEVALUE,
                file_get_contents($dir . 'latest')
            );
        }

        $header = curl_exec($curl);
        $this->info = curl_getinfo($curl);
        curl_close($curl);

        $filename = $dir . $this->info['filetime'].'.json';

        return (
            $this->info['http_code'] == 300 ||
            $this->fs->exists($filename)
        ) ? false : true;

    }

    /**
     * Requests the the `.json` file and save it.
     *
     * @param string $dir The directory where to save the output
     * @param string $url The url of the Json file.
     *
     * @return string filename of new file.
     */
    public function saveJson($dir, $url = null)
    {

        if (is_null($url)) {
            $url = $this->url;
        }

        if (is_null($this->info)) {
            $this->checkHeader($dir, $url);
        }

        // download newest and save it:
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);

        $filename = $dir . $this->info['filetime'].'.json';
        $this->fs->dumpFile($filename, $data);

        // save last timestamp in extra file:
        $this->fs->dumpFile($dir . 'latest', $this->info['filetime']);

        return $filename;
    }

}