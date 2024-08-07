<?php

namespace App\Components\Settings\Drivers;

use App\Components\Settings\Contracts\Driver;
use App\Components\Settings\Facades\PageSettings;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\ForwardsCalls;

class FakedDriver implements Driver
{
    use ForwardsCalls;

    /**
     * Decorated driver
     */
    protected ?Driver $decoratedDriver = null;

    /**
     * Initializes the driver.
     */
    public function __construct(
        public readonly array $settings
    ) {}

    /**
     * {@inheritDoc}
     */
    public function setting(string $page, $setting, $default = null)
    {
        return $this->hasFakedSetting($page, $setting) ?
                $this->getFakedSetting($page, $setting, $default) :
                $this->resolveDriver()->setting($page, $setting, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function settings(string $page, ...$keys)
    {
        $keys = ! is_array($keys[0]) ? $keys : $keys[0];

        $settings = [];

        foreach ($keys as $key) {
            $settings[$key] = $this->setting($page, $key);
        }

        return $settings;
    }

    /**
     * {@inheritDoc}
     */
    public function all(string $page)
    {
        return array_merge($this->resolveDriver()->all($page), $this->getFakedSettings($page));
    }

    /**
     * Forwards call to underlying driver.
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
     * Creates array key for accessing faked settings.
     */
    protected function createArrayKey(string $page, string $key): string
    {
        return "{$page}.{$key}";
    }

    /**
     * Checks if setting is faked.
     *
     * @return bool
     */
    protected function hasFakedSetting(string $page, string $key)
    {
        return Arr::has($this->settings, $this->createArrayKey($page, $key));
    }

    /**
     * Gets faked setting.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    protected function getFakedSetting(string $page, $key, $default = null)
    {
        return Arr::get($this->settings, $this->createArrayKey($page, $key), $default);
    }

    /**
     * Gets faked settings
     *
     * @return array
     */
    protected function getFakedSettings(string $page)
    {
        return Arr::get($this->settings, $page, []);
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
