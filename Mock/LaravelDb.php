<?php

namespace WebmanTech\LaravelValidation\Mock;

use support\Db;

/**
 * @internal
 */
final class LaravelDb extends Db
{
    public static function getInstance()
    {
        return static::$instance;
    }
}
