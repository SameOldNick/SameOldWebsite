<?php

namespace App\Components\Moderator;

use App\Components\Moderator\Commands\UpdateCommand;
use App\Components\Moderator\Contracts\Moderator;
use Illuminate\Contracts\Container\Container;
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
        $this->app->bind(ModerationService::class, function (Container $app) {
            $config = $app->config->get('moderators', []);

            $name = $config['builder'];
            $builder = $config['builders'][$name];

            $factory = $app->make($builder['factory']);

            return new ModerationService($factory);
        });

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
