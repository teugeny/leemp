<?php

ini_set('display_errors', false);
define('AUTOLOAD_EXTENSIONS', '.php');
define("LEVORIUM_PATH",dirname(dirname(__FILE__)));
define("LEVORIUM_VENDORS",dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendors');

require_once dirname(dirname(__FILE__))
    . DIRECTORY_SEPARATOR . 'vendors'
    . DIRECTORY_SEPARATOR . 'Twig'
    . DIRECTORY_SEPARATOR . 'Autoloader.php';

require 'ScanDirectory.php';

/**
 * Class Bootstrap
 * version 1.2
 */
class Bootstrap
{
    /** @var array  */
    private $paths = [];

    /**
     * @var
     */
    private static $instance;

    private $loaded;

    /**
     * @return Bootstrap
     */
    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param $directory
     * @return array
     */
    public function scanDirectory($directory)
    {
        return ScanDirectory::getFiles($directory);
    }

    /**
     * Add new path to loader
     * Path should be full
     * @param $path
     * @return $this
     */
    public function addPath($path)
    {
        if (!in_array($path,$this->paths))
        {
            array_push($this->paths,$path);
        };
        return $this;
    }

    /**
     * Load
     */
    public function load()
    {
        if (!$this->loaded)
        {
            spl_autoload_register(function ($className)
            {
                foreach($this->paths as $directory) {
                    $path = $directory . DIRECTORY_SEPARATOR . $className . AUTOLOAD_EXTENSIONS;
                    if ( file_exists($path) ) { include $path; }
                }
            });
            $this->loaded = true;
        }
    }

    /**
     * Load default files
     */
    private function getPackage()
    {
        $package = json_decode(file_get_contents(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'package.json'));

        foreach ($package as $item) {
            $this->addPath(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . $item);
        }
    }

    /**
     * Bootstrap constructor.
     */
    private function __construct()
    {
        $this->getPackage();
        Twig_Autoloader::register();
    }

    private function __clone() {}
}