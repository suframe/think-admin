<?php

namespace suframe\thinkAdmin\traits;

trait SingleInstance
{
    static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (static::$instance) {
            return static::$instance;
        }
        return static::$instance = new static();
    }
}