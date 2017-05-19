<?php

/**
 * Class Model
 */
abstract class Model
{
    /**
     * Data which we want to save
     *
     * @var
     */
    protected $data;
    /**
     * @var array
     */
    protected static $_instance = [];

    /**
     * Name of the class now we use
     *
     * @var string
     */
    protected static $className;

    /**
     * @var array
     */
    private static $whereRows = [];

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        $className = get_called_class();
        self::$className = $className;
        if (@!(self::$_instance[$className] instanceof $className)) {
            self::$_instance[$className] = new $className();
        }

        return self::$_instance[$className];
    }


    /**
     * @param $what
     * @param $ratio
     * @param $target
     * @return mixed
     */
    public static function where($what, $ratio, $target)
    {
        self::$whereRows = [
            'what' => $what,
            'ratio' => $ratio,
            'target' => $target
        ];

        return self::getInstance();
    }

    /**
     * @return array
     */
    public function get()
    {
        if (count(self::$whereRows) > 0) {
            $where = self::$whereRows;
            return DB::table(self::$className)->where($where['what'],$where['ratio'],$where['target'])->get();
        } else {
            return DB::table(self::$className)->get();
        }
    }

    /**
     * @return mixed
     */
    public static function getAll()
    {
        return self::getInstance()->getAllRows();
    }

    /**
     * @return integer|null
     * Return ID of created record if it's success or null if it's fail
     */
    public function save()
    {
        if (!self::$className) { self::getInstance(); }
        $rows = [];
        array_push($rows,$this->data);


        return (DB::table(self::$className)->insert($rows) != null)
            ? DB::table(self::$className)
                ->runQuery("select * FROM wp_".self::$className." ORDER BY id DESC LIMIT 1")->id
            : null;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $where = self::$whereRows;

        if (count($where) > 0) {
            $response = DB::table(self::$className)
                ->where($where['what'],$where['ratio'],$where['target'])
                ->delete();

        } else {
            $response = false;
        }

        return $response;
    }

    /**
     * @param $what
     * @param $ratio
     * @param $target
     * @return bool
     */
    public function update($what, $ratio, $target)
    {
        $rows = [];
        array_push($rows,$this->data);
        $result = true;

        foreach ($this->data as $key => $value) {
            $DBRequest = DB::table(self::$className)->where($what, $ratio, $target)->update($key,$value);
            if (strlen($DBRequest) != 0 && $DBRequest != true) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function __set( $name , $value )
    {
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return null;
    }

    /**
     * @return array
     */
    private function getAllRows()
    {
        return DB::table(self::$className)->get();
    }

    /**
     * @param $what
     * @param $ratio
     * @param $target
     * @return array
     */
    private function getRawsWhere($what, $ratio, $target)
    {
        return DB::table(self::$className)
            ->where($what,$ratio,$target)
            ->get();
    }
}