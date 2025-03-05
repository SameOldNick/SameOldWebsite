<?php

namespace App\Components\Captcha\Providers\Settings;

use App\Components\Captcha\Contracts\Providers\SettingsProvider;
use Illuminate\Support\Arr;

class ConfigProvider implements SettingsProvider
{
    /**
     * Constructs a new config provider.
     */
    public function __construct(
        protected readonly array $config
    ) {}

    /**
     * {@inheritDoc}
     */
    public function defaultDriver(): string
    {
        return Arr::get($this->config, 'default', 'recaptcha');
    }

    /**
     * {@inheritDoc}
     */
    public function availableDrivers(): array
    {
        return array_keys(Arr::get($this->config, 'drivers', []));
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $driver, ?string $key = null, mixed $default = null): mixed
    {
        return Arr::get($this->config, !is_null($key) ? "drivers.{$driver}.{$key}" : "drivers.{$driver}", $default);
    }
}
