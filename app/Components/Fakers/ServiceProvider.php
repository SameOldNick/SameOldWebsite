<?php

namespace App\Components\Fakers;

use Faker\Generator;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Str;
use BladeUI\Icons\Factory as BladeIconsFactory;

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

        $this->app->bind(Providers\BladeIcon::class, function ($app) {
            return new Providers\BladeIcon(
                $app->make(Generator::class),
                $app->make(BladeIconsFactory::class),
                $app->make(FilesystemFactory::class),
            );
        });

        $this->app->afterResolving(function ($object, $app) {
            $class = is_object($object) ? get_class($object) : $object;

            if (Str::startsWith($class, Generator::class)) {
                $object->addProvider($app->make(Providers\SocialMedia::class));
                $object->addProvider($app->make(Providers\BladeIcon::class));
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
