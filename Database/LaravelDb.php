<?php

namespace WebmanTech\LaravelValidation\Database;

use Illuminate\Database\Capsule\Manager;

class LaravelDb extends Manager
{
    /**
     * @return object|Manager
     */
    public static function getManagerInstance()
    {
        return static::$instance;
    }
}