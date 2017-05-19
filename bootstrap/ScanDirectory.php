<?php

/**
 * Class ScanDirectory
 */
class ScanDirectory
{
    /**
     * @var string
     */
    private static $rootDir;
    /**
     * @var array
     */
    private $files = [];

    /**
     * @param null $directory
     * @return array
     */
    public static function getFiles($directory = null)
    {
        self::$rootDir = $directory . DIRECTORY_SEPARATOR;
        return self::getInstance()->search($directory);
    }

    /**
     * @param $directory
     * @return array
     */
    private function search($directory)
    {
        $this->getFilesFromDirectory($directory);
        return $this->files;
    }

    /**
     * @param $directory
     */
    private function getFilesFromDirectory($directory)
    {
        $list = array_map(
            function($item) use ($directory)
            {
                return $directory . DIRECTORY_SEPARATOR . $item;
            },
            scandir($directory)
        );

        foreach ($list as $item)
        {
            if (preg_match('/(.php)/',$item))
            {
                $item = $this->getPathFromRoot($this->getPath($item));
                if (!in_array($item,$this->files))
                {
                    array_push($this->files,$item);
                }
            }
            elseif(!preg_match('/(\/\.)|(.php)/',$item) && is_dir($item))
            {
                $this->getFilesFromDirectory($item);
            }
        }
    }

    /**
     * @param $path string
     * @return string
     */
    private function getPathFromRoot($path)
    {
        $path = str_replace(self::$rootDir,"",$path) . DIRECTORY_SEPARATOR;
        return $path != self::$rootDir
            ? $path
            : DIRECTORY_SEPARATOR;
    }

    /**
     * @param $file
     * @return string
     */
    private function getPath($file)
    {
        $path = "";
        $separated = explode(DIRECTORY_SEPARATOR,$file);
        for ($i = 1; $i < count($separated) -1; $i++)
        {
            $path .= DIRECTORY_SEPARATOR . $separated[$i];
        }
        return $path;
    }

    /**
     * @return ScanDirectory
     */
    private function getInstance()
    {
        return new self();
    }

    /**
     * ScanDirectory constructor.
     */
    private function __construct(){}
}