<?php

namespace App\Components\Captcha\Contracts\Providers;

interface SettingsProvider
{
    /**
     * Gets the default driver name.
     *
     * @return string
     */
    public function defaultDriver(): string;

    /**
     * Gets the list of available drivers.
     *
     * @return list<string>
     */
    public function availableDrivers(): array;

    /**
     * Gets a setting value for a driver.
     *
     * @param string $driver The driver name.
     * @param string|null $key If null, the entire driver configuration is returned.
     * @param mixed $default
     * @return mixed
     */
    public function get(string $driver, ?string $key = null, mixed $default = null): mixed;
}
