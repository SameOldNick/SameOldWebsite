<?php

namespace App\Components\Backup;

use App\Components\Backup\Contracts\BackupSchedulerConfigurationProvider;
use App\Components\Backup\Config\DatabaseConfig;
use App\Components\Backup\Config\DatabaseConfigProvider;
use App\Components\Backup\Contracts\BackupConfigurationProvider;
use App\Components\Backup\Contracts\ConfigProvider;
use App\Components\Backup\Contracts\NotificationConfigurationProviderInterface;
use App\Components\Backup\DbDumper\MySqlPHP;
use App\Components\Backup\Providers\BackupDatabaseConfigurationProvider;
use App\Components\Backup\Providers\BackupSchedulerDatabaseConfigurationProvider;
use App\Components\Backup\Providers\DatabaseNotificationConfigurationProvider;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Spatie\Backup\Config\Config;
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
        $this->app->extend(Config::class, function (Config $config, Container $app) {
            return $app->make(ConfigProvider::class, ['config' => $config]);
        });

        $this->bindConfigurationProviders();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootDbDumperExtender();
        $this->scheduleBackups();
    }

    /**
     * Binds configuration provider interfaces to implementations.
     *
     * @return void
     */
    protected function bindConfigurationProviders()
    {
        $this->app->bind(NotificationConfigurationProviderInterface::class, DatabaseNotificationConfigurationProvider::class);
        $this->app->bind(BackupSchedulerConfigurationProvider::class, BackupSchedulerDatabaseConfigurationProvider::class);
        $this->app->bind(ConfigProvider::class, DatabaseConfigProvider::class);
        $this->app->bind(BackupConfigurationProvider::class, BackupDatabaseConfigurationProvider::class);
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
    protected function scheduleBackups()
    {
        // TODO: Check if app is setup
        if ($this->app->runningInConsole()) {
            $this->app->make(BackupScheduler::class)->schedule();
        }
    }
}
