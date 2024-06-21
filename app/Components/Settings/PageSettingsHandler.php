<?php

namespace App\Components\Settings;

use App\Components\Settings\Contracts\Driver;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Handles settings for a specific page
 */
class PageSettingsHandler implements Arrayable
{
    /**
     * Intializes handler
     */
    public function __construct(
        public readonly string $page,
        protected readonly Driver $driver
    ) {}

    /**
     * Gets setting value
     *
     * @param  string  $setting  Key
     * @param  mixed  $default
     * @return mixed
     */
    public function setting($setting, $default = null)
    {
        return $this->getDriver()->setting($this->page, $setting, $default);
    }

    /**
     * Gets settings as array
     *
     * @param  mixed  ...$args  Keys
     * @return array
     */
    public function settings(...$args)
    {
        return $this->getDriver()->settings($this->page, ...$args);
    }

    /**
     * Get all settings.
     *
     * @return array
     */
    public function all()
    {
        return $this->getDriver()->all($this->page);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->all();
    }

    /**
     * Gets the underlying driver.
     */
    public function getDriver(): Driver
    {
        return $this->driver;
    }
}
