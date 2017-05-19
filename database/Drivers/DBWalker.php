<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))) .DIRECTORY_SEPARATOR. 'wp-load.php';
/**
 * The layer above WPdb class
 * Temporary solution
 * Class DBWalker
 */
class DBWalker
{
    /**
     * @var wpdb
     */
    private $db;

    /**
     * @param $query
     * @return array|null|object
     */
    public static function get($query)
    {
        return self::getInstance()->request($query);
    }

    /**
     * @return DBWalker
     */
    private static function getInstance()
    {
        return new self();
    }

    /**
     * @param $query
     * @return array|null|object
     */
    private function request($query)
    {
        return $this->db->get_results($query);
    }

    /**
     * WpDBWalker constructor.
     */
    private function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }
}