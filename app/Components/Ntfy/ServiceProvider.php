<?php

namespace App\Components\Ntfy;

use App\Components\Ntfy\Channels\NtfyChannel;
use App\Components\Ntfy\Services\Ntfy;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Ntfy::class, function ($app) {
            $config = config('services.ntfy', []);

            return new Ntfy($config);
        });

        $this->app->alias(Ntfy::class, 'ntfy');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(ChannelManager $manager)
    {
        $manager->extend('ntfy', function ($app) {
            return new NtfyChannel($app->make(Ntfy::class));
        });
    }
}
