<?php

namespace WebmanTech\LaravelValidation\Facades;

use Illuminate\Container\Container;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Contracts\Validation\Factory as FactoryContract;
use Illuminate\Validation\DatabasePresenceVerifierInterface;
use WebmanTech\LaravelValidation\Factory;
use WebmanTech\LaravelValidation\Helper\ConfigHelper;
use WebmanTech\LaravelValidation\Helper\ExtComponentGetter;

/**
 * @method static \Illuminate\Validation\Validator make(array $data, array $rules, array $messages = [], array $attributes = [])
 * @method static array validate(array $data, array $rules, array $messages = [], array $attributes = [])
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

    public static function reset(): void
    {
        self::$_instance = null;
    }

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
        $factory = new Factory(static::getTranslator(), Container::getInstance());
        // registerPresenceVerifier
        if ($dbPresence = static::createDatabasePresenceVerifier()) {
            $factory->setPresenceVerifier($dbPresence);
        }
        // registerUncompromisedVerifier 涉及到需要远程调用api，暂时不考虑

        return $factory;
    }

    protected static function createDatabasePresenceVerifier(): ?DatabasePresenceVerifierInterface
    {
        return ExtComponentGetter::get(DatabasePresenceVerifierInterface::class);
    }

    public static function getTranslator(): TranslatorContract
    {
        return ExtComponentGetter::get(TranslatorContract::class);
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
