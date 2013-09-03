<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marco
 * Date: 28.08.13
 * Time: 10:00
 * To change this template use File | Settings | File Templates.
 */

namespace Freifunk\StatisticBundle\Importer;


use Exception;

/**
 * Class ImportException
 *
 * @package Freifunk\StatisticBundle\Importer
 */
class ImportException extends \Exception
{
    /** @var string  */
    private $key;

    /**
     * Constructor
     *
     * @param string $key
     * @param null   $message
     *
     * @return ImportException
     */
    public function __construct($key, $message = null)
    {
        if ($message == null) {
            $message = '"' . $key . '" key missing or invalid';
        }
        parent::__construct($message);
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getWrongKey()
    {
        return $this->key;
    }
}