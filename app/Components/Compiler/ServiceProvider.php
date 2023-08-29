<?php

namespace App\Components\Compiler;

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
        $this->app->singleton(CompilerManager::class, function ($app) {
            return new CompilerManager($app);
        });

        $this->app->alias(CompilerManager::class, 'compiler');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
