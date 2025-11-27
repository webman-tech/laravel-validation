<?php

namespace WebmanTech\LaravelValidation\Helper;

use WebmanTech\CommonUtils\Container;

/**
 * @internal
 * 通过 scripts/generate_base_ext_component_getter.php 生成，请勿直接修改
 */
abstract class BaseExtComponentGetter
{
    /**
     * @var array<string, array{0: array, 1: \Closure}>
     */
    private static array $componentsDefine = [];
    /**
     * @var array<string, mixed>
     */
    private static array $components = [];

    /**
     * @return array<string, array{alias?: string[], singleton?: \Closure}>
     */
    abstract protected static function getDefine(): array;

    /**
     * @return array<string, array{0: array, 1: \Closure}>
     */
    private static function getDefinedComponents(): array
    {
        if (!self::$componentsDefine) {
            $define = static::getDefine();

            $container = Container::getCurrent();

            foreach ($define as $className => $options) {
                $ids = array_merge([$className], $options['alias'] ?? []);
                $componentGetter = null;
                foreach ($ids as $id) {
                    if ($container->has($id)) {
                        $componentGetter = fn() => $container->get($id);
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
     * @template TClass of object
     * @param string|class-string<TClass> $need
     * @return ($need is class-string<TClass> ? TClass|null : mixed)
     */
    public static function get(string $need): mixed
    {
        $component = self::$components[$need] ?? null;
        if ($component === null) {
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
        }

        return $component === '__NULL__' ? null : $component;
    }
}
