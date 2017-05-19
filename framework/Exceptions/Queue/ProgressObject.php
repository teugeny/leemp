<?php

/**
 * Class ProgressObject
 */
class ProgressObject
{
    /**
     * ProgressObject constructor.
     */
    public function __construct() {}

    /**
     * @return ProgressObject
     */
    public static function create()
    {
        return new self;
    }

    /**
     * @return stdClass
     */
    final public function get()
    {
        $object = new stdClass();
        list($object->total, $object->done, $object->completed) = array(0,0,0);
        foreach (array('finished','succeed') as $key) { $object->{$key} = false;}
        return $object;
    }

}