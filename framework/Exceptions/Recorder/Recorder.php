<?php

/**
 * Class Recorder
 */
class Recorder
{
    /**
     * Start timer
     * @var string
     */
    private static $start;

    /**
     * Instance of current class
     * @var
     */
    private static $instance;

    /**
     * @param $message
     */
    public static function write($message)
    {
        self::getInstance()->writeContent($message);
    }

    /**
     * Start timer
     */
    public static function startTimer()
    {
        self::$start = microtime(true);
    }

    /**
     * Stop timer and print difference
     * @param bool $write
     */
    public static function stopTimer($write = true)
    {
        $time = microtime(true) - self::$start;
        $message = "Time up {$time} \n";
        if ($write) {
            self::getInstance()->writeContent($message);
        } else {
            echo $message;
        }
    }

    /**
     * @return Recorder
     */
    private static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param $message
     */
    private function writeContent($message)
    {
        $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'data.txt';

        $content = (file_exists($file))
            ? file_get_contents($file)
            : "";

        $content .= "{$message} \n";

        file_put_contents($file,$content);
    }

    /**
     * Recorder constructor.
     */
    private function __construct() {}

    /**
     *
     */
    private function __clone() {}
}