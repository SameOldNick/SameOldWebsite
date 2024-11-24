<?php

namespace App\Components\MFA;

use App\Components\MFA\Contracts\SecretStore;
use App\Components\MFA\Services\Authenticator\AuthenticatorService;
use App\Components\MFA\Services\Persist\PersistService;
use App\Components\MFA\Stores\Eloquent\SecretStore as EloquentSecretStore;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
        $this->app->singleton('mfa.auth', function (Application $app) {
            return new AuthenticatorService($app);
        });

        $this->app->singleton('mfa.persist', function (Application $app) {
            return new PersistService($app);
        });

        $this->app->alias('mfa.auth', AuthenticatorService::class);
        $this->app->alias('mfa.persist', PersistService::class);

        $this->app->bind(SecretStore::class, EloquentSecretStore::class);

        Route::mixin(new RouteMethods);
    }

    /**
     * Boot any application services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::extend('mfa', function (Application $app) {
            $inner = $app['auth.driver'];
            $authenticator = $app['mfa.auth'];
            $persist = $app['mfa.persist'];

            return new Guard($inner, $authenticator, $persist);
        });

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadViewsFrom(__DIR__ . '/views', 'mfa');
    }
}
