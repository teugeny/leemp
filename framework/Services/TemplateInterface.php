<?php

/**
 * Interface TemplateInterface
 */
interface TemplateInterface
{
    public static function me();

    public function setData($data);

    public function render();
}