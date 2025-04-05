<?php

namespace App\Components\Captcha;

use App\Components\Captcha\Contracts\Providers\SettingsProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SettingsProvider::class, function ($app) {
            return new Providers\Settings\ConfigProvider($app['config']['captcha']);
        });

        $this->app->alias(CaptchaService::class, 'captcha');

        $this->registerViews();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component('captcha', Components\CaptchaComponent::class);
    }

    /**
     * Register the package views.
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/views', 'captcha');
    }
}
