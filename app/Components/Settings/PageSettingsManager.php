<?php

namespace App\Components\Settings;

use App\Models\Page;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;
use Illuminate\Support\Traits\Macroable;

/**
 * This class manages and returns driver instances for handling page settings.
 * @method mixed setting(string $page, $setting, $default = null) Gets setting value for page
 * @method array settings(string $page, $settings) Gets setting values for page
 * @method array<string, mixed> all(string $page) Gets all settings for page
 */
class PageSettingsManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->shouldUseCache() ? 'cache' : 'eloquent';
    }

    /**
     * Set the current page to retrieve settings for.
     *
     * @param string $page The page identifier.
     * @param ?string $driver
     * @return PageSettingsHandler
     */
    public function page(string $page, string $driver = null) {
        return new PageSettingsHandler($page, $this->driver($driver));
    }

    /**
     * Creates eloquent driver
     *
     * @return Drivers\EloquentDriver
     */
    protected function createEloquentDriver()
    {
        return $this->container->make(Drivers\EloquentDriver::class);
    }

    /**
     * Creates cache driver
     *
     * @return Drivers\CacheDriver
     */
    protected function createCacheDriver()
    {
        return $this->container->make(Drivers\CacheDriver::class);
    }

    /**
     * Checks if cache driver should be used.
     *
     * @return boolean
     */
    protected function shouldUseCache(): bool {
        return $this->getContainer()->isProduction();
    }
}
