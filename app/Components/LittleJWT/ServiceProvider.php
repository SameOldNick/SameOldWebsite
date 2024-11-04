<?php

namespace App\Components\LittleJWT;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use LittleApps\LittleJWT\Factories\KeyBuilder;
use LittleApps\LittleJWT\LittleJWT;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('littlejwt.refresh', function ($app) {
            /**
             * The key needs to be pulled from the config file because Laravel
             * doesn't load the .env file when the config is cached.
             */
            $config = config('littlejwt.key.refresh', [
                'type' => 'secret',

                'options' => [
                    'phrase' => '',
                ],
            ]);

            $jwk = KeyBuilder::buildFromConfig([
                'default' => $config['type'],
                $config['type'] => $config['options'],
            ]);

            return new LittleJWT($app, $jwk);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {}
}
