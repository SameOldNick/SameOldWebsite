<?php

namespace App\Components\Settings;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PageSettingsManager::class, function ($app) {
            return new PageSettingsManager($app);
        });

        $this->app->alias(PageSettingsManager::class, 'pagesettings');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {}
}
