<?php

/**
 * Class Task
 */
abstract class Task {

    /** @var  bool */
    private $success;

    /** @var  stdClass */
    private $progress;

    /** @var QueueListener  */
    private $listener;

    /**
     * @var bool
     */
    private $fail = false;

    /** @var DataStorage */
    private $storage;

    private $result;

    private $options;

    /** @var array  */
    protected static $_instance = [];

    /** Run current task abstract method */
    abstract public function run();

    /**
     * @return mixed
     */
    abstract public function getName();

    /** Check current progress status of the task */
    public function checkIn()
    {
        $this->listener->checkIn();
    }

    /**
     * @return mixed
     */
    public static function create()
    {
        $className = get_called_class();
        if (@!(self::$_instance[$className] instanceof $className)) {
            self::$_instance[$className] = new $className();
        }
        return self::$_instance[$className];
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @param $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return $this
     */
    public function setFail()
    {
        $this->fail = true;
        $this->success = false;
        return $this;
    }

    public function isFail()
    {
        return $this->fail;
    }

    /**
     * @return $this
     */
    public function finalize()
    {
        $this->success = true;

        return $this;
    }

    /**
     * @param $storage
     * @return $this
     */
    public function setStorage(DataStorage $storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return mixed
     */
    public function getStorageAllData()
    {
        return $this->storage->getAllData();
    }

    /**
     * @return mixed
     */
    public function getPreviewsTaskResults()
    {
        return end($this->storage->getAllData());
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getStorageDataByKey($key)
    {
        return $this->storage->getData($key);
    }

    /**
     * @param $listener
     * @return $this
     */
    public function setListener(QueueListener $listener)
    {
        $this->listener = $listener;
        $this->progress = $this->getProgressObject();
        return $this;
    }

    /**
     * @return stdClass
     */
    public function getProgressObject()
    {
        return ProgressObject::create()->get();
    }

    /**
     * @param $total
     * @param $done
     * @return stdClass
     */
    public function calculateProgress($total, $done)
    {
        list($this->progress->total,$this->progress->done) = [$total,$done];
        $this->progress->completed = ($done != 0) ? $done / ($total/100) : 0;
        if ( $this->progress->completed == 100 ) {
            $this->progress->finished = true;
            if (!$this->isFail()) {
                $this->progress->succeed = true;
            }
        }
        return $this->progress;
    }

    /**
     * @param $result
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    private function __construct()
    {
        $this->progress = new stdClass();
    }
}