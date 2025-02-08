<?php

namespace WebmanTech\LaravelValidation\Helper;

/**
 * @internal
 */
final class ConfigHelper
{
    /**
     * 获取配置
     * @param string $key
     * @param $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return config("plugin.webman-tech.laravel-validation.{$key}", $default);
    }
}
