<?php

namespace App\Components\Encryption;

use App\Components\Encryption\Commands\GenerateKeyCommand;
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
        $this->app->singleton(Signer::class, function ($app) {
            return new Signer($app);
        });

        $this->app->alias(Signer::class, 'encryption.signer');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Artisan::starting(function ($artisan) {
            $artisan->resolve(GenerateKeyCommand::class);
        });
    }
}
