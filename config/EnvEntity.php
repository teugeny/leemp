<?php

/**
 * Class EnvEntity
 */
class EnvEntity
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private static $values;

    /**
     * @param $name string
     * @param $values array
     * @return $this
     */
    public static function create($name,$values)
    {
        return self::getInstance()
            ->setName($name)
            ->setValues($values);
    }

    /**
     * @return array
     */
    public static function get()
    {
        return self::getInstance()->getValues();
    }

    /**
     * @param $name string
     * @return $this
     */
    private function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    private function getValues()
    {
        return self::$values;
    }

    /**
     * @param $values array
     * @return $this
     */
    private function setValues($values)
    {
        self::$values = $values;
        return $this;
    }

    /**
     * @return EnvEntity
     */
    private function getInstance()
    {
        return new self();
    }
}