<?php

/**
 * Interface ReportWalkerInterface
 */
interface ReportWalkerInterface
{
    public static function getInstance();
    public function setData($data);
    public function addReportItem($item);
    public function save();
    public function get();
    public function getReport($what, $ratio, $target);
}