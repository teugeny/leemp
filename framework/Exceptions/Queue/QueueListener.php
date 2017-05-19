<?php

/**
 * Class QueueListener
 */
class QueueListener
{
    /** @var ProgressLogger  */
    public $logger;

    /** @var array  */
    public $subscribes = [];

    /** @var int  */
    private $progress = 0;

    /** @var   */
    private static $instance;

    /**
     * @return QueueListener
     */
    public static function getInstance()
    {
        if ( null === self::$instance )
        {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone() {}

    /**
     * QueueListener constructor.
     */
    public function __construct()
    {
        $this->logger = ProgressLogger::getInstance();
    }

    /**
     * @param $tasks
     */
    public function addSubscribes($tasks)
    {
        foreach ($tasks as $task) {
            $subs = new stdClass();
            $subs->progress = $this->getProgressObject();
            $subs->progress->name = $task->getName();
            $subs->task = $task;
            array_push($this->subscribes,$subs);
        }
    }

    /**
     * @return array
     */
    public function getSubscribes()
    {
        return $this->subscribes;
    }

    public function getProgressData()
    {
        return $this->logger->getProgressData();
    }
    /**  */
    public function checkIn()
    {
        foreach ($this->subscribes as $subscribe) {
            foreach ($subscribe->task->getProgress() as $key => $value) {
                $subscribe->progress->{$key} = $value;
            }
            $this->progress = round(($this->progress += $subscribe->progress->completed - $this->progress),2);
        }
        $this->logger->setData($this->getSubscribesProgress())->save();

        if ( $this->progress == 100 ) {
            $this->logger->free();
        }
    }

    /**
     * @return int
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setLoggerPath($path)
    {
        $this->logger->setLogPath($path);
        return $this;
    }

    /**
     * @return array|mixed|object
     */
    public function getDataFromLogFile()
    {
        return $this->logger->readLogFile();
    }

    /**
     * @return array
     */
    public function getSubscribesProgress()
    {
        $progress = [];
        foreach ($this->subscribes as $subscribe) {
            array_push($progress,$subscribe->progress);
        }
        return $progress;
    }

    /**
     * @return stdClass
     */
    private function getProgressObject()
    {
        return ProgressObject::create()->get();
    }
}