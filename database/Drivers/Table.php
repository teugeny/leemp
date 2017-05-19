<?php

/**
 * Class Table
 */
final class Table
{
    /**
     * @var stdClass
     */
    private $table;

    /**
     * @return Table
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->table->name = $name;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function increments($name)
    {
        array_push($this->table->rows,$this->addRow("INT AUTO_INCREMENT NOT NULL",$name));
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function integer($name)
    {
        array_push($this->table->rows,$this->addRow('INT DEFAULT 0',$name));
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function date($name)
    {
        array_push($this->table->rows,$this->addRow('DATE',$name));
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function test($name)
    {
        array_push($this->table->rows,$this->addRow('TEXT',$name));
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function string($name)
    {
        array_push($this->table->rows,$this->addRow("STRING",$name));
        return $this;
    }

    /**
     * @param $name
     * @param $count integer
     * @return $this
     */
    public function varchar($name, $count)
    {
        array_push($this->table->rows,$this->addRow("VARCHAR({$count})",$name));
        return $this;
    }

    /**
     * @return stdClass
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param $type
     * @param $name
     * @param $default
     * @return stdClass
     */
    private function addRow($type, $name, $default = null)
    {
        $row = new stdClass();
        $row->name = $name;
        $row->type = $type;
        $row->default = $default;
        return $row;
    }

    /**
     * Table constructor.
     */
    private function __construct()
    {
        $this->table = new stdClass();
        $this->table->rows = [];
    }
}