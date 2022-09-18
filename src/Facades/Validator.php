<?php

namespace WebmanTech\LaravelValidation\Facades;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Factory as FactoryContract;
use WebmanTech\LaravelValidation\Factory;

/**
 * @method static \Illuminate\Contracts\Validation\Validator make(array $data, array $rules, array $messages = [], array $customAttributes = [])
 * @method static void excludeUnvalidatedArrayKeys()
 * @method static void extend(string $rule, \Closure|string $extension, string $message = null)
 * @method static void extendImplicit(string $rule, \Closure|string $extension, string $message = null)
 * @method static void replacer(string $rule, \Closure|string $replacer)
 * @method static array validate(array $data, array $rules, array $messages = [], array $customAttributes = [])
 *
 * @see \Illuminate\Validation\Factory
 * @see \Illuminate\Support\Facades\Validator
 */
class Validator
{
    /**
     * @var null|FactoryContract
     */
    protected static $_instance = null;
    /**
     * @var null|Translator
     */
    protected static $_translator = null;

    public static function instance(): FactoryContract
    {
        if (!static::$_instance) {
            $factory = static::createFactory();
            $extends = config('plugin.webman-tech.laravel-validation.app.extends');
            if ($extends instanceof \Closure) {
                call_user_func($extends, $factory);
            }
            static::$_instance = $factory;
        }
        return static::$_instance;
    }

    protected static function createFactory(): FactoryContract
    {
        return new Factory(static::getTranslator());
    }

    protected static function getTranslator(): Translator
    {
        if (!static::$_translator) {
            $translator = config('plugin.webman-tech.laravel-validation.app.translation');
            if ($translator instanceof \Closure) {
                $translator = call_user_func($translator);
            }
            static::$_translator = $translator;
        }
        return static::$_translator;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::instance()->{$name}(... $arguments);
    }
}