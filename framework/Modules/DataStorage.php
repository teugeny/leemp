<?php

/**
 * Class DataStorage
 */
class DataStorage {

    /** @var array  */
    private $data = [];

    /** @var   */
    private static $instance;

    /**
     * @return DataStorage
     */
    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __clone() {}

    private function __construct() {}

    /**
     * @param $key
     * @param $data
     * @return $this
     */
    public function pushData($key, $data)
    {
        $this->data[$key] = $data;
        return $this;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getData($key)
    {
        return $this->data[$key];
    }

    /**
     * @return array
     */
    public function getAllData()
    {
        return $this->data;
    }
    /**
     * @param $key
     */
    public function removeData($key)
    {
        unset($this->data[$key]);
    }
}