<?php

namespace App\Components\Dusk;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Dusk\Console\DuskCommand;
use App\Components\Dusk\Console\DuskCommand as ExtendedDuskCommand;

final class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->extend(DuskCommand::class, function () {
            return new ExtendedDuskCommand();
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
