<?php

namespace App\Components\Settings\Facades;

use App\Components\Settings\PageSettingsManager;
use App\Components\Settings\Testing\PageSettingsManager as TestingPageSettingsManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Components\Settings\PageSettingsHandler page(string $key)
 * @method static \App\Components\Settings\Contracts\Driver driver(?string $name)
 */
class PageSettings extends Facade
{
    /**
     * Fakes the page settings
     * Examples:
     *   PageSettings::fake('home', ['key' => 'value']);
     *   PageSettings::fake(['home' => ['key' => 'value']]);
     *
     * @param  array<string,array>|string  $page  Array of settings to fake or the page key to fake.
     * @param  array|null  $settings  Settings to fake. Only used if $page is a string.
     * @return TestingPageSettingsManager
     */
    public static function fake($page, ?array $settings = null)
    {
        $pageSettings = is_string($page) && is_array($settings) ? [$page => $settings] : (array) $page;

        static::swap($faked = new TestingPageSettingsManager(static::getFacadeApplication(), $pageSettings));

        return $faked;
    }

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return PageSettingsManager::class;
    }
}
