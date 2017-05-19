<?php

/**
 * Class Schema
 */
final class Schema
{
    /**
     * @var Table
     */
    private $table;

    /**
     * @param $table
     * @param Closure $closure
     * @return Schema
     */
    public static function create($table, Closure $closure)
    {
        return self::getInstance($table, $closure);
    }

    /**
     * @param $table
     */
    public static function drop($table)
    {
        self::dropSchema($table);
    }

    /**
     * @param $table
     * @param $closure
     * @return Schema
     */
    public static function getInstance($table, $closure)
    {
        return new self($table, $closure);
    }

    /**
     * @param $table
     */
    public function dropSchema($table)
    {
        $prefifx = Env::me()->get('DB')['prefix'];
        DB::getInstance()->runQuery("DROP TABLE IF EXISTS {$prefifx}{$table}");
    }

    /**
     * Build schema method
     */
    public function buildSchema()
    {
        DB::getInstance()->runQuery($this->prepareQuery());
    }

    /**
     * @return string
     */
    private function prepareQuery()
    {
        $table = $this->table->getTable();
        $prefifx = Env::me()->get('DB')['prefix'];
        $query = "CREATE TABLE {$prefifx}{$table->name} ( ";

        foreach ($table->rows as $row) {
            $query .= "`{$row->name}` {$row->type}";
            $query .= $row->default != null
                ? " DEFAULT {$row->default},"
                : ",";
        }

        $query .= " PRIMARY KEY(id))";

        return $query;
    }

    /**
     * Schema constructor.
     * @param $table
     * @param Closure $closure
     */
    private function __construct($table, Closure $closure)
    {
        $this->table = Table::create()->setName($table);
        if ($closure != null) {
            $closure($this->table);
            $this->buildSchema();
        }
    }
}