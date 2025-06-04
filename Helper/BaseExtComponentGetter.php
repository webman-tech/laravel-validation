<?php

namespace WebmanTech\LaravelValidation\Helper;

use support\Container;

/**
 * @internal
 * 通过 scripts/generate_base_ext_component_getter.php 生成，请勿直接修改
 */
abstract class BaseExtComponentGetter
{
    private static array $componentsDefine = [];
    private static array $components = [];

    abstract protected static function getDefine(): array;

    private static function getDefinedComponents(): array
    {
        if (!self::$componentsDefine) {
            $define = static::getDefine();

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
     * @return (T is class-string ? T|null : mixed)
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
