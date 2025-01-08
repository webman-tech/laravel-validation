<?php

namespace WebmanTech\LaravelValidation\Facades;

use Illuminate\Container\Container;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Factory as FactoryContract;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Validation\DatabasePresenceVerifierInterface;
use WebmanTech\LaravelValidation\Database\LaravelDb;
use WebmanTech\LaravelValidation\Factory;
use WebmanTech\LaravelValidation\Helper\ConfigHelper;
use WebmanTech\LaravelValidation\Translation\NullTranslator;
use WebmanTech\LaravelValidation\Translation\WebmanSymfonyTranslator;

/**
 * @method static \Illuminate\Validation\Validator make(array $data, array $rules, array $messages = [], array $attributes = [])
 * @method static array validate( $data,  $rules,  $messages = [],  $attributes = [])
 * @method static void extend(string $rule, \Closure|string $extension, string|null $message = null)
 * @method static void extendImplicit(string $rule, \Closure|string $extension, string|null $message = null)
 * @method static void extendDependent(string $rule, \Closure|string $extension, string|null $message = null)
 * @method static void replacer(string $rule, \Closure|string $replacer)
 * @method static void includeUnvalidatedArrayKeys()
 * @method static void excludeUnvalidatedArrayKeys()
 * @method static void resolver(\Closure $resolver)
 * @method static \Illuminate\Validation\PresenceVerifierInterface getPresenceVerifier()
 * @method static void setPresenceVerifier(\Illuminate\Validation\PresenceVerifierInterface $presenceVerifier)
 * @method static \Illuminate\Contracts\Container\Container|null getContainer()
 * @method static \Illuminate\Validation\Factory setContainer(\Illuminate\Contracts\Container\Container $container)
 *
 * @see \Illuminate\Validation\Factory
 * @see \Illuminate\Support\Facades\Validator
 */
class Validator
{
    protected static ?FactoryContract $_instance = null;
    protected static ?Translator $_translator = null;

    public static function instance(): FactoryContract
    {
        if (!static::$_instance) {
            $factory = static::createFactory();
            $extends = ConfigHelper::get('app.extends');
            if ($extends instanceof \Closure) {
                call_user_func($extends, $factory);
            }
            static::$_instance = $factory;
        }
        return static::$_instance;
    }

    /**
     * https://github.com/laravel/framework/blob/11.x/src/Illuminate/Validation/ValidationServiceProvider.php
     * @return FactoryContract
     */
    protected static function createFactory(): FactoryContract
    {
        // registerValidationFactory
        $factory = new Factory(static::getTranslator(), Container::setInstance());
        // registerPresenceVerifier
        if ($dbPresence = static::createDatabasePresenceVerifier()) {
            $factory->setPresenceVerifier($dbPresence);
        }
        // registerUncompromisedVerifier 涉及到需要远程调用api，暂时不考虑

        return $factory;
    }

    protected static function createDatabasePresenceVerifier(): ?DatabasePresenceVerifierInterface
    {
        if (class_exists('Illuminate\Database\Capsule\Manager') && LaravelDb::getManagerInstance()) {
            return new DatabasePresenceVerifier(LaravelDb::getManagerInstance()->getDatabaseManager());
        }
        return null;
    }

    public static function getTranslator(): Translator
    {
        if (!static::$_translator) {
            $translator = ConfigHelper::get('app.translation');
            if ($translator instanceof \Closure) {
                $translator = call_user_func($translator);
            }
            if (!$translator) {
                $translator = (function (): Translator {
                    // laravel-translation
                    if (class_exists('WebmanTech\LaravelTranslation\Facades\Translator')) {
                        return \WebmanTech\LaravelTranslation\Facades\Translator::instance();
                    }
                    // webman 中使用的 symfony/translation
                    if (class_exists('Symfony\Component\Translation\Translator')) {
                        return new WebmanSymfonyTranslator();
                    }

                    return new NullTranslator();
                })();
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
