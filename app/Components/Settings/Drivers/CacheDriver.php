<?php

namespace App\Components\Settings\Drivers;

use Illuminate\Support\Arr;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Contracts\Cache\Repository;

class CacheDriver {
    use ForwardsCalls;

    protected $pageKey;
    protected $eloquentDriver;
    protected $eloquentDriverFactory;
    protected $cache;

    /**
     * Initializes Page Settings
     *
     * @param Page $page
     */
    public function __construct(string $pageKey, callable $eloquentDriverFactory, Repository $cache) {
        $this->pageKey = $pageKey;
        $this->eloquentDriverFactory = $eloquentDriverFactory;
        $this->cache = $cache;
    }

    /**
     * Gets setting value
     *
     * @param string $setting Key
     * @param mixed $default
     * @return mixed
     */
    public function setting($setting, $default = null)
    {
        return Arr::get($this->toArray(), $setting, $default);

        /*return $this->cache->get($this->createCacheKey($setting), function () use ($setting, $default) {
            $value = $this->resolveEloquentDriver()->setting($setting, $default);

            if ($value !== $default)
                $this->cache->set($this->createCacheKey($setting), $value);

            return $value;
        });*/
    }

    /**
     * Gets settings as array
     *
     * @param mixed ...$args Keys
     * @return array
     */
    public function settings(...$args)
    {
        $keys = ! is_array($args[0]) ? $args : $args[0];

        return Arr::only($this->toArray(), $keys);
    }

    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray()
    {
        return $this->cache->get($this->createCacheKey(), function () {
            $all = $this->resolveEloquentDriver()->toArray();

            $this->cache->set($this->createCacheKey(), $all);

            return $all;
        });
    }

    public function __call($name, $arguments)
    {
        return $this->forwardDecoratedCallTo($this->resolveEloquentDriver(), $name, $arguments);
    }

    /**
     * Creates cache key that hold settings.
     *
     * @return string
     */
    protected function createCacheKey() {
        return "pages.{$this->pageKey}";
    }

    /**
     * Resolves eloquent driver
     *
     * @return EloquentDriver
     */
    protected function resolveEloquentDriver() {
        if (is_null($this->eloquentDriver)) {
            $this->eloquentDriver = \call_user_func($this->eloquentDriverFactory);
        }

        return $this->eloquentDriver;
    }
}
