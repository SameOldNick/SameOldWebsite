<?php

namespace App\Components\Settings;

use App\Models\Page;
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
        $this->app->singleton(ContactPageSettings::class, function ($app) {
            $page = Page::firstWhere(['page' => 'contact']);

            return new ContactPageSettings($page);
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
