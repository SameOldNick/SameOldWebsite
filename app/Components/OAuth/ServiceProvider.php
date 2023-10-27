<?php

namespace App\Components\OAuth;

use App\Components\OAuth\Socialite\SocialiteManager;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Contracts\Factory;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(OAuth::class, function($app) {
            return $this->setupDrivers(new OAuth($app));
        });

        $this->app->alias(OAuth::class, 'oauth');

        $this->app->extend(Factory::class, function (Factory $factory, Container $app) {
            return new SocialiteManager($app);
        });

        Route::mixin(new OAuthRouteMethods);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    protected function setupDrivers(OAuth $oauth) {
        foreach ($this->getDrivers() as $driver => $concrete) {
            $oauth->extend($driver, function ($app) use ($concrete) {
                return $app->make($concrete);
            });
        }

        return $oauth;
    }

    protected function getDrivers() {
        return [
            'github' => Drivers\GitHub::class,
            'google' => Drivers\Google::class,
            'twitter' => Drivers\Twitter::class,
        ];
    }
}
