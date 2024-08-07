<?php

namespace App\Components\Settings\Drivers;

use App\Components\Settings\Contracts\Driver;
use App\Components\Settings\Facades\PageSettings;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\ForwardsCalls;

class CacheDriver implements Driver
{
    use ForwardsCalls;

    /**
     * Cache repository
     */
    protected readonly Repository $cache;

    /**
     * Decorated driver
     */
    protected ?Driver $decoratedDriver = null;

    /**
     * Initializes the cache driver
     *
     * @param  Repository  $cache  Cache repository
     */
    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function setting(string $page, $setting, $default = null)
    {
        return Arr::get($this->all($page), $setting, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function settings(string $page, ...$keys)
    {
        $keys = ! is_array($keys[0]) ? $keys : $keys[0];

        return Arr::only($this->all($page), $keys);
    }

    /**
     * {@inheritDoc}
     */
    public function all(string $page)
    {
        return $this->cache->get($this->createCacheKey($page), function () use ($page) {
            $all = $this->resolveDriver()->all($page);

            $this->cache->set($this->createCacheKey($page), $all);

            return $all;
        });
    }

    /**
     * Forwards calls to underlying driver.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->forwardDecoratedCallTo($this->resolveDriver(), $name, $arguments);
    }

    /**
     * Creates cache key that hold settings.
     *
     * @return string
     */
    protected function createCacheKey(string $page)
    {
        return "pages.{$page}";
    }

    /**
     * Resolves driver
     *
     * @return Driver
     */
    protected function resolveDriver()
    {
        if (is_null($this->decoratedDriver)) {
            $this->decoratedDriver = PageSettings::driver('eloquent');
        }

        return $this->decoratedDriver;
    }
}
