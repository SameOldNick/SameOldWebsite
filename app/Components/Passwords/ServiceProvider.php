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
    public function register() {}

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        LaravelPassword::defaults(
            Password::createFromCallback(function (PasswordRulesBuilder $builder) {
                /**
                 * @var \Illuminate\Foundation\Application
                 */
                $app = $this->app;

                $default = $app->config->get('passwords.default', 'production');

                $builder->fromConfig($app->config->get("passwords.rules.{$default}", []));
            })
        );
    }
}
