<?php

namespace App\Components\SweetAlert;

use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
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
        $this->app->singleton(SweetAlerts::class, function ($app) {
            $session = $app->make(SessionManager::class);

            $existing = $session->get('sweetalerts', []);

            return new SweetAlerts($existing);
        });

        $this->app->alias(SweetAlerts::class, 'sweetalerts');
        $this->app->alias(SweetAlerts::class, 'swal');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component('sweetalerts', SweetAlertsView::class);
        //View::composer('components.sweetalerts', SweetAlertComposer::class);
    }
}
