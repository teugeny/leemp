<?php

/**
 * Class Migration
 */
class Migration
{
    /**
     * @var MigrationInterface
     */
    private $migration;

    /**
     * @param $migration
     */
    public static function up($migration)
    {
        self::getInstance($migration)->buildTable();
    }

    /**
     * @param $migration
     */
    public static function down($migration)
    {
        /**
         * $var $migration MigrationInterface
         */
        self::getInstance($migration)->dropTable();
    }

    /**
     * @param $migration
     * @return Migration
     */
    public static function getInstance($migration)
    {
        return new self($migration);
    }

    /**
     * Migration constructor.
     * @param $migration
     */
    private function __construct($migration)
    {
        $this->migration = new $migration();
    }

    /**
     * Run drop method of the migration class
     */
    private function dropTable()
    {
        $this->migration->down();
    }

    /**
     * Run build method of the migration class
     */
    private function buildTable()
    {
        $this->migration->up();
    }
}