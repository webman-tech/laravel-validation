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
     * @var array<string, array{0: string[], 1: \Closure|null}>
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
     * @return array<string, array{0: string[], 1: \Closure|null}>
     */
    private static function getDefinedComponents(): array
    {
        if (!self::$componentsDefine) {
            foreach (static::getDefine() as $className => $options) {
                $ids = array_merge([$className], $options['alias'] ?? []);
                // 仅缓存解析定义；容器命中与否在 get() 时实时探测（支持宿主后绑）
                foreach ($ids as $id) {
                    self::$componentsDefine[$id] = [$ids, $options['singleton'] ?? null];
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
        $componentDefine = self::getDefinedComponents()[$need] ?? null;
        if ($componentDefine === null) {
            throw new \InvalidArgumentException($need . ' is not defined');
        }
        [$ids, $closure] = $componentDefine;

        // 容器实时探测优先（has 为轻量数组查询）：宿主绑定永远生效，包括首次访问后才绑定的场景
        $container = Container::getCurrent();
        foreach ($ids as $id) {
            if ($container->has($id)) {
                return $container->get($id);
            }
        }

        // 闭包兜底结果缓存复用（默认实现 singleton 语义）
        $component = self::$components[$need] ?? null;
        if ($component === null) {
            if ($closure === null) {
                throw new \InvalidArgumentException('cannot find component ' . $need);
            }
            $component = $closure();
            if ($component !== null && !$component instanceof $ids[0]) {
                throw new \RuntimeException(sprintf('%s resolve failed, got %s', $ids[0], get_debug_type($component)));
            }
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
