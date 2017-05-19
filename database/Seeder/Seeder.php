<?php

/**
 * Class Seeder
 */
class Seeder
{
    /**
     * @var SeederInterface
     */
    private $seeder;
    /**
     * @param $seeder
     */
    public static function run($seeder)
    {
        self::getInstance($seeder)->runSeeder();
    }

    /**
     * @param $seeder
     * @return Seeder
     */
    public static function getInstance($seeder)
    {
        return new self($seeder);
    }

    private function runSeeder()
    {
        $this->seeder->run();
    }

    /**
     * Seeder constructor.
     * @param $seeder
     */
    private function __construct($seeder)
    {
        $this->seeder = new $seeder();
    }
}