<?php

/**
 * Class Garbage
 */
class Garbage {

    /** @var array  */
    private $garbage = [];

    private $test = 0;

    /**
     * @return Garbage
     */
    public static function getInstance()
    {
        return new self();
    }

    /**
     * @param $garbage string
     */
    public function add($garbage)
    {
        $this->test++;
        array_push($this->garbage,$garbage);
        $this->writeToStorageFile();
    }

    /**
     * @return array
     */
    public function getGarbageFiles()
    {
        return json_decode(file_get_contents($this->getStorageFilePath()));
    }

    /**  */
    public function erase()
    {
        $this->add($this->getStorageFilePath());

        foreach ($this->getGarbageFiles() as $item) {
            (is_file($item)) ? $this->removeFile($item) : $this->deleteAll($item);
        }
    }

    /**
     * @param $path
     * @return bool
     */
    private function isFile($path)
    {
        $file_type = end(explode(".",end(explode(DIRECTORY_SEPARATOR,$path))));
        return ($file_type != null) ? true : false;
    }

    /**
     * @param $file
     */
    private function removeFile($file)
    {
        unlink($file);
        $path = explode(DIRECTORY_SEPARATOR,$file);
        array_pop($path);
        $dir = '';
        for ($i = 1; $i < count($path); $i++){
            $dir .= DIRECTORY_SEPARATOR.$path[$i];
        }
        $files = (file_exists($dir)) ? scandir($dir) : null;
        if ($files != null) {
            for ($i = 0; $i < 2; $i++) {
                array_shift($files);
            }
            if (count($files) == 0) {
                rmdir($dir);
            }
        }
    }

    /**
     * @param $directory
     * @param bool $empty
     * @return bool
     */
    private function deleteAll($directory, $empty = false)
    {
        if(substr($directory,-1) == "/") {
            $directory = substr($directory,0,-1);
        }

        if(!file_exists($directory) || !is_dir($directory)) {
            return false;
        } elseif(!is_readable($directory)) {
            return false;
        } else {
            $directoryHandle = opendir($directory);

            while ($contents = readdir($directoryHandle)) {
                if($contents != '.' && $contents != '..') {
                    $path = $directory . "/" . $contents;

                    if(is_dir($path)) {
                        $this->deleteAll($path);
                    } else {
                        unlink($path);
                    }
                }
            }

            closedir($directoryHandle);

            if($empty == false) {
                if(!rmdir($directory)) {
                    return false;
                }
            }

            return true;
        }
    }

    /**
     * @return string
     */
    private function getStorageFilePath()
    {
        $env = Env::me()->get("TEMP");
        $path = isset($env)
            ? $env
            : dirname(dirname(__FILE__));
        return $path . DIRECTORY_SEPARATOR. '.garbage.json';
    }

    /** Write json file with files paths */
    private function writeToStorageFile()
    {
        $file = $this->getStorageFilePath();
        $content = (file_exists($file)) ? json_decode(file_get_contents($file)) : [];
        foreach ($this->garbage as $item) {
            array_push($content,$item);
        }
        file_put_contents($file,json_encode($content));

    }

    public function __construct() {}
}