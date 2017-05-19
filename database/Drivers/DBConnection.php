<?php

/**
 * Class DBConnection
 */
class DBConnection
{
    /**
     * @var mysqli
     */
    private static $connection;

    /**
     * Current instance of class
     * @var
     */
    private static $instance;

    /**
     * @var mixed
     */
    private $settings;

    /**
     * @return mysqli
     */
    public static function get()
    {
        return self::getInstance()->getCurrentConnection();
    }

    /**
     * @return DBConnection
     */
    private function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return mysqli
     */
    private function getCurrentConnection()
    {
        return self::$connection;
    }

    /**
     * DBConnection constructor.
     */
    private function __construct()
    {
        $this->settings = Env::me()->get('DB');
        self::$connection = mysqli_connect(
            $this->settings['host'],
            $this->settings['user'],
            $this->settings['pass'],
            $this->settings['db'],
            $this->settings['port'],
            $this->settings['socket']);

        $this->prefix = Env::me()->get('DB')['prefix'];
    }

    private function __clone() {}
}