<?php

namespace App\Components\Security;

use App\Components\Security\Commands\WatchdogCommand;
use Illuminate\Console\Application as Artisan;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

final class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Watchdog::class, function ($app) {
            return new Watchdog($app);
        });

        $this->app->alias(Watchdog::class, 'watchdog');

        $this->app->register(Integrations\ServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Artisan::starting(function (Artisan $artisan) {
            $artisan->resolve(WatchdogCommand::class);
        });
    }
}
