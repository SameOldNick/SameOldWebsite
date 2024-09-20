<?php

namespace App\Components\Backup;

use App\Components\Backup\Contracts\NotificationConfigurationProviderInterface;
use App\Components\Backup\DbDumper\MySqlPHP;
use App\Components\Backup\Providers\DatabaseNotificationConfigurationProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Spatie\Backup\Tasks\Backup\DbDumperFactory;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(NotificationConfigurationProviderInterface::class, DatabaseNotificationConfigurationProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        DbDumperFactory::extend('mysql', function () {
            return new MySqlPHP;
        });
    }
}
