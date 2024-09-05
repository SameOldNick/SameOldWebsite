<?php

namespace App\Components\Moderator;

use App\Components\Moderator\Commands\UpdateCommand;
use Illuminate\Console\Application as Artisan;
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
        $this->app->alias(ModerationService::class, 'moderator');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Artisan::starting(function (Artisan $artisan) {
            $artisan->resolve(UpdateCommand::class);
        });
    }
}
