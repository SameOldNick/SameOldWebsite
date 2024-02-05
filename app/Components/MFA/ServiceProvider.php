<?php

namespace App\Components\MFA;

use App\Components\MFA\Services\Authenticator\AuthenticatorManager;
use App\Components\MFA\Services\Authenticator\AuthenticatorService;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\Driver;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\AuthDriver;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\AuthServiceAdapter;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\SecretResolver;
use App\Components\MFA\Services\Persist\PersistService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('mfa.auth', function(Application $app) {
            return new AuthenticatorService($app);
        });

        $this->app->singleton('mfa.persist', function(Application $app) {
            return new PersistService($app);
        });

        $this->app->alias('mfa.auth', AuthenticatorService::class);
        $this->app->alias('mfa.persist', PersistService::class);

        Route::mixin(new RouteMethods);
    }

    /**
     * Boot any application services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::extend('mfa', function(Application $app) {
            $inner = $app['auth.driver'];
            $authenticator = $app['mfa.auth'];
            $persist = $app['mfa.persist'];

            return new Guard($inner, $authenticator, $persist);
        });


        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'mfa');
    }
}
