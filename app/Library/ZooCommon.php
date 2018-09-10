<?php

namespace App\Library;

use Zookeeper;

class ZooCommon
{
    private $zoo;
    private $zooConf;
    private static $instance;

    protected function __construct()
    {
        $this->zoo = new Zookeeper();
        $this->zooConf = '192.168.152.133:2181';
        $this->zoo->connect($this->zooConf);
    }

    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function __clone(){}

    public function get($key)
    {
        return $this->zoo->get($key);
    }
}
