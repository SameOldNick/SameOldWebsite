<?php

namespace App\Components\OAuth;

use App\Components\OAuth\Contracts\OAuthFlowHandler as OAuthFlowHandlerContract;
use App\Components\OAuth\Handlers\OAuthFlowHandler;
use App\Components\OAuth\Socialite\SocialiteManager;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
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
        $this->app->singleton(OAuth::class, function ($app) {
            return $this->setupDrivers(new OAuth($app));
        });

        $this->app->alias(OAuth::class, 'oauth');

        $this->app->extend(Factory::class, function (Factory $factory, Container $app) {
            return new SocialiteManager($app);
        });

        $this->app->bind(OAuthFlowHandlerContract::class, OAuthFlowHandler::class);

        Route::mixin(new OAuthRouteMethods);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {}

    /**
     * Sets up OAuth drivers
     *
     * @param  OAuth  $oauth  OAuth manager
     * @return OAuth
     */
    protected function setupDrivers(OAuth $oauth)
    {
        foreach ($this->getDrivers() as $driver => $concrete) {
            $oauth->extend($driver, function ($app) use ($concrete) {
                return $app->make($concrete);
            });
        }

        return $oauth;
    }

    /**
     * Gets OAuth drivers
     *
     * @return array
     */
    protected function getDrivers()
    {
        return [
            'github' => Providers\GitHub::class,
            'google' => Providers\Google::class,
            'twitter' => Providers\Twitter::class,
        ];
    }
}
