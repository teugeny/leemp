<?php

/**
 * Class Environment
 */
class Environment
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @var array
     */
    private static $EntityInstances;

    /**
     * @var string
     */
    private static $currentInstanceName;

    /**
     * @var EnvEntity
     */
    private static $currentInstance;

    /**
     * @var array
     */
    private $defaults = [
        'default' => 1
    ];

    /**
     * @var array
     */
    private $settings = [];

    /**
     * @return Environment
     */
    public static function me()
    {
        if (self::$instance === null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param $name string
     * @param $values array
     * @return Environment
     */
    public static function setup($name,$values)
    {
        self::me()->buildEntity($name,$values);
        return self::$instance;
    }

    /**
     * @return $this
     */
    public function init()
    {
        $this->switchCurrentInstance(self::$currentInstanceName);
        $entity = self::$currentInstance;
        $this->settings = $this->defaults;
        foreach ($entity::get() as $key => $value) {
            $this->settings[$key] = $value;
        }
        return $this;
    }

    /**
     * @param $name string
     * @return mixed
     */
    public function get($name)
    {
        print_r(self::$EntityInstances);
        return $this->settings[$name];
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->settings;
    }

    /**
     * @param $name string
     * @param $values array
     * @return Environment
     */
    private function buildEntity($name,$values)
    {
        self::$currentInstanceName = $name;
        self::$EntityInstances[$name] = EnvEntity::create($name,$values);
        return self::$instance;
    }

    /**
     * @param $name string
     * @return $this
     */
    private function switchCurrentInstance($name)
    {
        echo "$name \n";
        self::$currentInstance = self::$EntityInstances[$name];
        return $this;
    }

    private function __construct(){}
}


// Environment::setup([])->load();
// Environment::setup("Parser",[])->init();