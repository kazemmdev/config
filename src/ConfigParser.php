<?php

declare(strict_types=1);

namespace Kazemmdev\Config;

use Closure;
use ArrayAccess;

class ConfigParser
{
    /**
     * @param array|ArrayAccess $value
     * @return bool
     */
    public static function accessible($value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * @param array|ArrayAccess $array
     * @param mixed $key
     * @return bool
     */
    public static function exists($array, mixed $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * @param array|ArrayAccess $array
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($array, mixed $key, mixed $default = null): mixed
    {
        if (!static::accessible($array)) {
            return static::value($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (strpos($key, '.') === false) {
            return $array[$key] ?? static::value($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return static::value($default);
            }
        }

        return $array;
    }

    /**
     * @param array|ArrayAccess $array
     * @param mixed $key
     * @param mixed $value
     * @return mixed
     */
    public static function set(&$array, mixed $key, mixed $value): mixed
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        foreach ($keys as $index => $item) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$index]);

            if (!isset($array[$item]) || !is_array($array[$item])) {
                $array[$item] = [];
            }

            $array = &$array[$item];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public static function value(mixed $value): mixed
    {
        return $value instanceof Closure ? $value() : $value;
    }
}
