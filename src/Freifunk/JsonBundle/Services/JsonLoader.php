<?php
/**
 * Created by PhpStorm.
 * User: frederik
 * Date: 8/22/13
 * Time: 1:59 PM
 */

namespace Freifunk\JsonBundle\Services;

use Symfony\Component\Filesystem\Filesystem;

class JsonLoader
{

    /**
     * @var string Json Url
     */
    private $url;

    /**
     * @var array cUrl info of the request
     */
    private $info;

    /**
     * @var Filesystem Filesystem component
     */
    private $fs;

    /**
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->fs = new Filesystem();
    }

    /**
     * Requests the headers of the `.json` file to check whether or not we need to download it.
     *
     * @param $dir string The directory where to save the output
     * @param $url string The url of the Json file.
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
            curl_setopt($curl, CURLOPT_TIMECONDITION, CURL_TIMECOND_IFMODSINCE);
            curl_setopt($curl, CURLOPT_TIMEVALUE, file_get_contents($dir . 'latest'));
        }

        $header = curl_exec($curl);
        $this->info = curl_getinfo($curl);
        curl_close($curl);

        $filename = $dir . $this->info['filetime'].'.json';

        return ($this->info['http_code'] == 300 || $this->fs->exists($filename)) ? false : true;

    }

    /**
     * Requests the the `.json` file and save it.
     *
     * @param $dir string The directory where to save the output
     * @param $url string The url of the Json file.
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