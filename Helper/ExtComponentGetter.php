<?php

namespace WebmanTech\LaravelValidation\Helper;

use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Validation\ValidationServiceProvider;
use support\Container;
use Symfony\Component\Translation\Translator as SymfonyTranslator;
use WebmanTech\LaravelTranslation\Facades\Translator;
use WebmanTech\LaravelValidation\Mock\LaravelDb;
use WebmanTech\LaravelValidation\Translation\NullTranslator;
use WebmanTech\LaravelValidation\Translation\WebmanSymfonyTranslator;

/**
 * @internal
 */
final class ExtComponentGetter
{
    private static array $componentsDefine = [];
    private static array $components = [];

    private static function getDefinedComponents(): array
    {
        if (!self::$componentsDefine) {
            $define = [
                /** @see TranslationServiceProvider::register() */
                TranslatorContract::class => [
                    'alias' => ['translator'],
                    'singleton' => function () {
                        if (class_exists(Translator::class)) {
                            return Translator::instance();
                        }
                        if (class_exists(SymfonyTranslator::class)) {
                            return new WebmanSymfonyTranslator();
                        }
                        return new NullTranslator();
                    },
                ],
                /** @see DatabaseServiceProvider::registerConnectionServices() */
                'db' => [
                    'singleton' => function () {
                        if (class_exists('Illuminate\Database\Capsule\Manager') && class_exists('support\Db')) {
                            return LaravelDb::getInstance()->getDatabaseManager();
                        }
                        return null;
                    }
                ],
                /** @see ValidationServiceProvider::registerPresenceVerifier() */
                'validation.presence' => [
                    'singleton' => function () {
                        $db = self::get('db');
                        if (!$db instanceof ConnectionResolverInterface) {
                            return null;
                        }
                        return new DatabasePresenceVerifier($db);
                    }
                ],
            ];

            foreach ($define as $className => $options) {
                $ids = array_merge([$className], $options['alias'] ?? []);
                $componentGetter = null;
                foreach ($ids as $id) {
                    if (Container::has($id)) {
                        $componentGetter = fn() => Container::get($id);
                        break;
                    }
                }
                if ($componentGetter === null) {
                    $componentGetter = $options['singleton'] ?? null;
                }
                if ($componentGetter === null) {
                    throw new \InvalidArgumentException('cannot find component ' . $className);
                }
                // 将所有 id 注册为组件
                foreach ($ids as $id) {
                    self::$componentsDefine[$id] = [$ids, $componentGetter];
                }
            }
        }

        return self::$componentsDefine;
    }

    /**
     * @template T of class-string|string
     * @param T $need
     * @return T is class-string ? T : mixed
     */
    public static function get(string $need)
    {
        $component = self::$components[$need] ?? null;
        if ($component) {
            return $component === '__NULL__' ? null : $component;
        }

        $componentDefine = self::getDefinedComponents()[$need] ?? null;
        if ($componentDefine === null) {
            throw new \InvalidArgumentException($need . ' is not defined');
        }

        [$ids, $componentGetter] = $componentDefine;
        $component = $componentGetter();
        if ($component === null) {
            $component = '__NULL__';
        }
        // 将所有相关组件都注册上
        foreach ($ids as $id) {
            self::$components[$id] = $component;
        }

        return $component;
    }
}
