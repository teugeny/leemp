<?php

/**
 * Class ProgressLogger
 */
class ProgressLogger {

    /** @var bool  */
    private $locked = false;

    /** @var  array */
    private $data;

    /** @var  string */
    private $path;

    /** @var  string */
    private $log_file_path;

    /**
     * ProgressLogger constructor.
     */
    public function __construct() {}

    /**
     * @return ProgressLogger
     */
    public static function getInstance()
    {
        return new self();
    }

    /**
     * @return $this
     */
    public function getLogger()
    {
        return $this;
    }


    /**
     * @param $path
     * @return $this
     */
    public function setLogPath($path)
    {
        $this->path = $path . DIRECTORY_SEPARATOR . 'queue';
        return $this;
    }

    /**
     * @return $this
     */
    public function free()
    {
        $this->locked = false;
        $this->removeLogFile();
        return $this;
    }

    /**
     * @return $this
     */
    public function lock()
    {
        $this->locked = true;
        $this->createLogFile();
        return $this;
    }


    /**
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getProgressData()
    {
        return $this->data;
    }
    /**
     * Save current log file
     */
    public function save()
    {
        $this->log_file_path = $this->path . DIRECTORY_SEPARATOR . 'list.json';
        $log_file = fopen($this->log_file_path,"w");
        fwrite($log_file,json_encode($this->data));
        fclose($log_file);

    }

    /**
     * Read data from current log file
     * @return array|mixed|object
     */
    public function readLogFile()
    {
        $file_path = $this->path .DIRECTORY_SEPARATOR. 'list.json';
        return (file_exists($file_path)) ? file_get_contents($file_path) : 0;
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return (file_exists($this->path)) ? true : false;
    }

    /**
     * Create log file
     * @return bool
     */
    private function createLogFile()
    {

        if ( ! file_exists($this->path) ) {
            return mkdir($this->path);
        } else {
            return false;
        }
    }

    /**
     * Remove current log file and folder
     */
    private function removeLogFile()
    {
        Garbage::getInstance()->add($this->getPath());
    }

    private function getDefaultLogFilePath()
    {
        return $this->log_file_path = $this->path . DIRECTORY_SEPARATOR . 'list.json';
    }

    private function getPath()
    {
        return $this->path;
    }
}