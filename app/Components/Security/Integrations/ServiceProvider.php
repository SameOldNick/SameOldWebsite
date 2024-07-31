<?php

namespace App\Components\Security\Integrations;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Monolog\Logger as Monolog;

final class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        /**
         * The log driver needs to be registered immediately.
         * Registering the driver later will cause it to be not found and the log will be
         * filled with "Unable to create configured logger. Using emergency logger." messages.
         */
        $this->app['log']->extend('sentinel', function ($app, $config) {
            $level = $config['level'] ?? 'debug';
            $handler = new LogHandler($level);

            return new Monolog('sentinel', [$handler]);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
