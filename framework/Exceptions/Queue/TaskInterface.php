<?php

/**
 * Interface TaskInterface
 */
interface TaskInterface
{
    /**
     * @return mixed
     */
    public function run();

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param $total
     * @return mixed
     */
    public function setTotal($total);

    /**
     * @param $done
     * @return mixed
     */
    public function setDone($done);

    /**
     * @return mixed
     */
    public function getProgress();

    public function checkIn();
}