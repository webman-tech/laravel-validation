<?php

namespace WebmanTech\LaravelValidation\Database;

use Illuminate\Database\Capsule\Manager;

if (file_exists(__DIR__ . '/../../../../webman/database/src/Initializer.php')) {
    require_once __DIR__ . '/../../../../webman/database/src/Initializer.php';
}

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
