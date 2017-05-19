<?php

/**
 * Class DB
 * Class to work with Database
 * version 1.0 beta
 */
class DB
{
    /** @var mysqli  */
    private $db;

    /**
     * Settings for DB connection
     * @var array
     */
    private $settings;

    /** @var  string name of the database table */
    private static $dbTable;

    /**
     * @var array
     * Where params for a select request
     */
    private $whereParams = [];

    /** @var  int */
    private $limitRows;

    /** @var  string */
    private $customQuery;

    /** @var  array */
    private $selectValues = [];

    /** @var  array */
    private $updateValues = [];

    /**
     * @var array
     */
    private $likeQuery = [];

    /**
     * Tables prefix
     * @var
     */
    private $prefix;
    /**
     * @var array
     */
    private $insertRows = [];

    /**
     * @param $table
     * @return DB
     */
    public static function table($table)
    {
        self::$dbTable = $table;
        return self::getInstance();
    }

    public static function query($query)
    {
        return self::getInstance()->parseQueryResults($query);
    }

    public static function getInstance()
    {
        return new self();
    }

    /**
     * @return $this
     */
    public function select()
    {
        $this->selectValues = func_get_args();
        return $this;
    }


    /*
    public function query()
    {
        $this->customQuery = current(func_get_args());
        return $this;
    }
    */

    /**
     * @return array
     */
    public function get()
    {
        return $this->parseQueryResults($this->prepare("select"));
    }

    /**
     * @param $query
     * @return array
     */
    public function runQuery($query)
    {
        return $this->parseQueryResults($query);
    }

    public static function run()
    {
        return new self();
    }


    /**
     * @return bool
     */
    public function delete()
    {
        $this->rawQuery($this->prepare("delete"));
        return (!$this->get())
            ? true
            : false;
    }

    /**
     * @param $key
     * @param $value
     * @return bool|mysqli_result
     */
    public function update($key,$value)
    {
        $item = new stdClass();
        $item->key = $key;
        $item->value = $value;
        array_push($this->updateValues,$item);
        $query = $this->prepare("update");
        $result = $this->rawQuery($query);
        return $result;
    }

    /**
     * @param $rows
     * @return int|null
     */
    public function insert($rows)
    {
        $results = [];
        $result = 1;

        foreach ($rows as $row) {
            array_push($this->insertRows,$this->parseInsertRow($row));
            array_push($results,$this->rawQuery($this->prepare("insert")));
        }
        foreach ($results as $item) {
            if ($item != 1) {
                $result = null;
            }
        }

        return $result;
    }

    /**
     * @param $rows
     * @return array
     */
    private function parseInsertRow($rows)
    {
        $insertRaw = [];
        foreach ($rows as $key => $value)
        {
            $row = new stdClass();
            $row->key = $key;
            $row->value = $value;
            array_push($insertRaw,$row);
        }
        return $insertRaw;
    }
    /**
     * @param $what
     * @param $ratio
     * @param $target
     * @return $this
     */
    public function where($what, $ratio, $target)
    {
        $this->whereParams = [];
        array_push($this->whereParams,[
            'what' => $what,
            'ratio' => $ratio,
            'target' => $target
        ]);

        return $this;
    }

    /**
     * @param $what
     * @param $ratio
     * @param $target
     * @return $this
     */
    public function andWhere($what, $ratio, $target)
    {
        array_push($this->whereParams,[
            EnumLev::WHAT   => $what,
            EnumLev::RATIO  => $ratio,
            EnumLev::TARGET => $target
        ]);
        return $this;
    }

    /**
     * @param $what
     * @param $like
     * @return $this
     */
    public function like($what, $like)
    {
        array_push($this->likeQuery,[$what => $like]);
        return $this;
    }

    /**
     * @param $limit int
     * @return $this
     */
    public function limit($limit)
    {
        $this->limitRows = $limit;
        return $this;
    }

    /**
     * @param $result
     * @return null|object
     */
    public function fetch($result)
    {
        return mysqli_fetch_object($result);
    }

    /**
     * @param $result
     */
    public function free($result)
    {
        mysqli_free_result($result);
    }

    /**
     * @param $query
     * @return array|stdClass
     */
    private function parseQueryResults($query)
    {
        $rows = [];
        if ( $result = $this->rawQuery($query) )
        {
            while($row = $this->fetch($result))
            {
                array_push($rows,$row);
            }
            $this->free($result);
        }
        return count($rows) > 1
            ? $rows
            : current($rows);
    }

    /**
     * @return array
     */
    private function getWhereParams()
    {
        return $this->whereParams;
    }

    /**
     * @return string
     */
    private function prepare()
    {
        if ($this->customQuery != null) {
            return $this->customQuery;
        } else {
            $request = current(func_get_args());
            $query = "{$request} ";
            $table = $this->prefix.self::$dbTable;
            $where = $this->getWhereParams();

            if (count($this->selectValues) > 0) {
                foreach ($this->selectValues as $value) {
                    $query .= "$value,";
                }
                $query = substr($query,0,-1);
            }  else {
                if ($request == 'select') {
                    $query .= " * ";
                }

            }

            if ($request != 'update' && $request != "insert") {
                $query .= " FROM {$table}";
            } else {
                if ($request == "update") {
                    $query .= " {$table} SET ";
                    foreach ($this->updateValues as $item) {
                        $query .= "{$item->key} = '".str_replace("'",'"',$item->value)."' ";
                    }
                }
            }

            if ($request = "insert") {
                foreach ($this->insertRows as $raw)
                {
                    $query = "INSERT ";
                    $keys = "(";
                    $values = "VALUES (";
                    for ($i = 0; $i < count($raw) - 1; $i++)
                    {
                        $keys .= "`{$raw[$i]->key}`, ";
                        $values .= "'".str_replace("'",'"',$raw[$i]->value)."', ";
                    }
                    $last = $raw[count($raw) - 1];
                    $keys .= "`{$last->key}`)"; $values .= "'".str_replace("'",'"',$last->value)."')";
                    $query .= "INTO {$table} {$keys} {$values} ";
                }
            }

            if ($where != null) {
                if (count($where) > 1) {
                    $query .= " WHERE ({$where[0]['what']} {$where[0]['ratio']} '{$where[0]['target']}') ";
                    for ($i = 1; $i < count($where); $i++) {
                        $query .= "AND ({$where[$i]['what']} {$where[$i]['ratio']} '{$where[$i]['target']}') ";
                    }
                } else {
                    $query .= " WHERE {$where[0]['what']} {$where[0]['ratio']} '{$where[0]['target']}' ";
                }
            }

            if (count($this->likeQuery) == 1) {
                $item = current($this->likeQuery);
                foreach ($item as $key => $like) {
                    $query .= " WHERE {$key} LIKE '{$like}' ";
                }
            } elseif (count($this->likeQuery) > 1) {
                $first = current($this->likeQuery);
                foreach ($first as $key => $value) {
                    $query .= " WHERE ({$key} LIKE '{$value}') OR ";
                }
                for ($i = 1; $i < count($this->likeQuery); $i++)
                {
                    foreach ($this->likeQuery[$i] as $key => $like) {
                        $query .= "({$key} LIKE '{$like}')";
                    }
                }
            }

            if ($this->limitRows != null) {
                $query .= "LIMIT {$this->limitRows}";
            }

            //echo "$query \n";
            return $query;
        }
    }

    /**
     * @param $query
     * @return bool|mysqli_result
     */
    private function rawQuery($query)
    {
        return mysqli_query($this->db, $query);
    }

    /**
     * DB constructor.
     */
    private function __construct()
    {
        $this->settings = Env::me()->get('DB');
        $this->db = mysqli_connect(
            $this->settings['host'],
            $this->settings['user'],
            $this->settings['pass'],
            $this->settings['db'],
            $this->settings['port'],
            $this->settings['socket']);
        $this->prefix = Env::me()->get('DB')['prefix'];
        //$this->db = DBConnection::get();
    }

    /**  */
    private function __clone() {}
}