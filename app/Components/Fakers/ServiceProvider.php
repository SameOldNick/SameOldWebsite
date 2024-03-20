<?php

namespace App\Components\Fakers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Str;
use Faker\Generator;

final class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Providers\SocialMedia::class, function ($app) {
            return new Providers\SocialMedia(
                $app->make(Generator::class)
            );
        });

        $this->app->afterResolving(function ($object, $app) {
            $class = is_object($object) ? get_class($object) : $object;

            if (Str::startsWith($class, \Faker\Generator::class)) {
                $object->addProvider($app->make(Providers\SocialMedia::class));
            }
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
