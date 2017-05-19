<?php

/**
 * Class Queue
 */
class Queue {

    /** @var   */
    private static $instance;

    /** @var array  */
    public $tasks = [];

    /** @var  string */
    public $current_task;

    /** @var  stdClass */
    public $progress;

    /** @var QueueListener  */
    public $listener;

    /** @var   */
    private $logger;

    /** @var string */
    private $log_path;

    /** @var array  */
    private $tasks_results = [];

    /*** @var DataStorage */
    private $storage;

    private $success = true;

    /**
     * @return Queue
     */
    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return array
     */
    public function getProgress()
    {
        return $this->listener->getSubscribesProgress();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getTaskResult($name)
    {
        return $this->tasks_results[$name];
    }

    /**
     * @return bool|stdClass
     */
    public function run()
    {
        if ( !$this->isLocked() ) {
            $this->lock();
            $this->listener->addSubscribes($this->tasks);

            try {
                array_walk(
                    $this->tasks,
                    function(Task $task){
                        if ($this->isSuccess()) {
                            $task->setListener($this->listener)->setStorage($this->storage)->run();
                            $this->storage->pushData($task->getName(),$task->getResult());
                            if (!$task->isSuccess()) {
                                $this->setFailStatus();
                            }
                        }
                    }
                );
            } catch (Exception $e) {
                $this->free();
            }

            $this->free();

            return true;


        } else {
            return false;
        }
    }

    /**
     * @return array|mixed|object
     */
    public function getLoggerData()
    {
        return $this->listener->logger->readLogFile();
    }

    /**
     * @param $path
     * @return $this
     */
    public function setLogPath($path)
    {
        $this->log_path = $path;
        $this->listener->logger->setLogPath($this->getLogPath());
        return $this;
    }

    /**
     * @return string
     */
    public function getLogPath()
    {
        $temp = Env::me()->get("TEMP");
        return $this->log_path != null
            ? $this->log_path
            : $temp != null ? $temp : dirname(dirname(__FILE__));
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        $tasks = $this->listener->getDataFromLogFile();
        $finished = true;
        foreach ($tasks as $task) {
            if ( ! $task->finished ) { $finished = false; }
        }
        return $finished;
    }

    /**
     * @param string $task
     * @param null $params
     * @return $this
     */
    public function add($task, $params = null)
    {
        /**
         * @var $task Task
         */
        array_push($this->tasks, $task::create()->setOptions($params));
        return $this;
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->listener->logger->isLocked();
    }

    /**
     * @param DataStorage $dataStorage
     * @return $this
     */
    private function setStorage(DataStorage $dataStorage)
    {
        $this->storage = $dataStorage;
        return $this;
    }
    /**
     * @param QueueListener $queueListener
     * @return $this
     */
    private function setListener(QueueListener $queueListener)
    {
        $this->listener = $queueListener;
        return $this;
    }

    /** Clear queue */
    private function free()
    {
        $this->listener->logger->free();
    }

    /** Lock current queue */
    private function lock()
    {
        $this->listener->logger->lock();
    }

    /**
     * Queue constructor.
     */
    private function __construct()
    {
        $this->setListener(QueueListener::getInstance()->setLoggerPath($this->getLogPath()))
            ->setStorage(DataStorage::getInstance());
    }

    /**
     * @return bool
     */
    private function isSuccess()
    {
        return $this->success;
    }

    /**
     *
     */
    private function setFailStatus()
    {
        $this->success = false;
    }
}