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

class ImportException extends \Exception
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