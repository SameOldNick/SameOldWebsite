<?php

namespace App\Components\Menus;

use App\Components\Menus\Render\MenuComponent;
use Illuminate\Support\Facades\Blade;
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
        $this->app->singleton('menus', function ($app) {
            return new Menus;
        });

        $this->app->singleton('menus.render', function ($app) {
            return new Render\Manager($app);
        });

        $this->app->alias('menus', Menus::class);
        $this->app->alias('menus.render', Render\Manager::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component('menu', MenuComponent::class);
    }
}
