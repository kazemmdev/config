<?php

declare(strict_types=1);

namespace Kazemmdev\Config;

use ArrayAccess;

class ConfigRepository implements ArrayAccess
{
    /**
     * Repository Constructor
     *
     * @param array $items
     */
    private function __construct(protected array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Build a new Config Repository
     *
     * @param array $items
     * @return self
     */
    public static function build(array $items): self
    {
        return new self(items: $items);
    }

    /**
     * Does this key exist in the items array
     *
     * @param string $keys
     * @return bool
     */
    public function has(string $keys): bool
    {
        $keys = (array) $keys;

        if (empty($this->items) || empty($keys)) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $this->items;

            if (ConfigParser::exists($this->items, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (ConfigParser::accessible($subKeyArray) && ConfigParser::exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get a specific config value
     *
     * @param array|string $key
     * @param mixed $value
     * @return array|mixed
     */
    public function get($key, $value = null): mixed
    {
        if (is_array($key)) {
            return $this->getMany($key);
        }

        return ConfigParser::get($this->items, $key, $value);
    }

    /**
     * Get many configuration values.
     *
     * @param array $keys
     * @return array
     */
    public function getMany(array $keys): array
    {
        $config = [];

        foreach ($keys as $key => $value) {
            if (is_numeric($key)) {
                [$key, $value] = [$value, null];
            }

            $config[$key] = ConfigParser::get($this->items, $key, $value);
        }

        return $config;
    }

    /**
     * Set a given configuration value.
     *
     * @param array|string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value = null): void
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $item => $value) {
            ConfigParser::set($this->items, $item, $value);
        }
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param string $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param string $key
     * @return array|mixed
     */
    public function offsetGet($key): mixed
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param string $key
     * @return void
     */
    public function offsetUnset($key): void
    {
        $this->set($key, null);
    }
}
