<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marco
 * Date: 27.08.13
 * Time: 09:34
 * To change this template use File | Settings | File Templates.
 */

namespace Freifunk\StatisticBundle\Service;


use Exception;

class JsonImporterException extends \Exception
{
    private $key;

    public function __construct($key, $message = null)
    {
        if ($message == null) {
            $message = 'json is missing the "' . $key . '" key';
        }
        parent::__construct($message);
        $this->key = $key;
    }

    public function getWrongKey()
    {
        return $this->key;
    }

}