<?php

namespace App\Components\Passwords;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Validation\Rules\Password as LaravelPassword;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        LaravelPassword::defaults(
            PasswordFactory::createPasswordLazy(function (PasswordRules $rules) {
                /**
                 * @var \Illuminate\Foundation\Application
                 */
                $app = $this->app;

                if ($app->isProduction()) {
                    $config = config('passwords.rules.production', []);
                } else {
                    $config = config('passwords.rules.development', []);
                }

                $rules->fromConfig($config);
            })
        );
    }
}
