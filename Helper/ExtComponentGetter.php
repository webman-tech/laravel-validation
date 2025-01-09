<?php

namespace WebmanTech\LaravelValidation\Helper;

use support\Container;

/**
 * @internal
 */
class ExtComponentGetter
{
    /**
     * @template T of class-string
     * @param T $needClass
     * @param array $pick
     * @return T
     */
    public static function get(string $needClass, array $pick = [])
    {
        $component = null;
        if (Container::has($needClass)) {
            $component = Container::get($needClass);
        }
        if (!$component && $pick) {
            foreach ($pick as $className => $getInstance) {
                if ($className === 'default' || class_exists($className)) {
                    $component = $getInstance();
                    break;
                }
            }
        }
        if (!$component instanceof $needClass) {
            throw new \RuntimeException($needClass . ' is not exist');
        }

        return $component;
    }
}
