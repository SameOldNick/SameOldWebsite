<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole() || $this->app->runningUnitTests()) {
            $this->app->afterResolving(function ($object) {
                $class = is_object($object) ? get_class($object) : $object;

                if (Str::startsWith($class, \Faker\Generator::class)) {
                    $object->addProvider(new \Faker\Provider\Fakecar($object));
                    $object->addProvider(new \DavidBadura\FakerMarkdownGenerator\FakerProvider($object));
                }
            });
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();
    }
}
