<?php

namespace App\Components\Backup;

use App\Components\Backup\Contracts\BackupSchedulerConfigurationProvider;
use App\Components\Backup\Contracts\NotificationConfigurationProviderInterface;
use App\Components\Backup\DbDumper\MySqlPHP;
use App\Components\Backup\Providers\BackupSchedulerDatabaseConfigurationProvider;
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
        $this->app->bind(BackupSchedulerConfigurationProvider::class, BackupSchedulerDatabaseConfigurationProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootDbDumperExtender();
        $this->bootScheduler();
    }

    /**
     * Adds MySqlPHP to create MySQL dump
     *
     * @return void
     */
    protected function bootDbDumperExtender()
    {
        DbDumperFactory::extend('mysql', function () {
            return new MySqlPHP;
        });
    }

    /**
     * Sets up backup scheduler
     *
     * @return void
     */
    protected function bootScheduler()
    {
        if ($this->app->runningInConsole()) {
            $this->app->make(BackupScheduler::class)->schedule();
        }
    }
}
