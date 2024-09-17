<?php

namespace App\Components\LittleJWT;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use InvalidArgumentException;
use LittleApps\LittleJWT\Exceptions\MissingKeyException;
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
            try {
                $jwk = KeyBuilder::buildFromConfig([
                    'default' => KeyBuilder::KEY_SECRET,
                    KeyBuilder::KEY_SECRET => [
                        'phrase' => env('LITTLEJWT_KEY_PHRASE_REFRESH', ''),
                    ],
                ]);

                return new LittleJWT($app, $jwk);
            } catch (InvalidArgumentException) {
                throw new MissingKeyException;
            }
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {}
}
