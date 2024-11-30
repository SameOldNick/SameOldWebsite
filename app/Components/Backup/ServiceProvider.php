<?php

namespace App\Components\Backup;

use App\Components\Backup\Contracts\BackupConfigurationProvider;
use App\Components\Backup\Contracts\BackupSchedulerConfigurationProvider;
use App\Components\Backup\Contracts\ConfigProvider;
use App\Components\Backup\Contracts\FilesystemConfigurationFactory as FilesystemConfigurationFactoryContract;
use App\Components\Backup\Contracts\NotificationConfigurationProviderInterface;
use App\Components\Backup\DbDumper\MySqlPHP;
use App\Components\Backup\Filesystem\DynamicFilesystemManager;
use App\Components\Backup\Filesystem\FilesystemConfigurationFactory;
use App\Components\Backup\Providers\BackupDatabaseConfigurationProvider;
use App\Components\Backup\Providers\BackupSchedulerDatabaseConfigurationProvider;
use App\Components\Backup\Providers\DatabaseNotificationConfigurationProvider;
use App\Components\Backup\SpatieBackup\DatabaseConfigProvider;
use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Filesystem\Factory as FactoryContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        /**
         * The service provider may be called before the database is setup.
         * If the case, the app will fail so we'll let it pull it from the config
         * until the database is setup. This happens usually with testing.
         */
        $this->rebindSpatieBackupConfig();
        $this->extendFilesystemManager();

        $this->bindContracts();
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
     * Checks if database is ready for holding backup config
     */
    protected function isDatabaseSetup(): bool
    {
        try {
            return DB::isConnected() && Schema::hasTable('backup_config');
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Rebinds Spate Backup Config instance
     *
     * @return void
     */
    protected function rebindSpatieBackupConfig()
    {
        $hasReset = false;

        /**
         * Spatie registers Config as scoped, which makes it a singleton.
         * This causes the same values from the config file to be pulled,
         * even if the database has become available. So, we will forget
         * the instance and re-save it when the database becomes available.
         */
        $this->app->beforeResolving(Config::class, function ($abstract, $params, $app) use (&$hasReset) {
            if ($this->isDatabaseSetup() && ! $hasReset) {
                $app->forgetInstance(Config::class);

                $hasReset = true;
            }
        });

        $this->app->extend(Config::class, function (Config $config, Container $app) {
            return $this->isDatabaseSetup() ? $app->make(DatabaseConfigProvider::class, ['original' => $config]) : $config;
        });
    }

    /**
     * Extends Filesystem Manager
     *
     * @return void
     */
    protected function extendFilesystemManager()
    {
        $this->app->extend(FactoryContract::class, function (FactoryContract $manager, Container $app) {
            $factory = $app->make(FilesystemConfigurationFactoryContract::class);

            return new DynamicFilesystemManager($app, $factory);
        });
    }

    /**
     * Binds interfaces to implementations.
     *
     * @return void
     */
    protected function bindContracts()
    {
        $this->app->bind(NotificationConfigurationProviderInterface::class, DatabaseNotificationConfigurationProvider::class);
        $this->app->bind(BackupSchedulerConfigurationProvider::class, BackupSchedulerDatabaseConfigurationProvider::class);
        $this->app->bind(ConfigProvider::class, DatabaseConfigProvider::class);
        $this->app->bind(BackupConfigurationProvider::class, BackupDatabaseConfigurationProvider::class);
        $this->app->bind(FilesystemConfigurationFactoryContract::class, FilesystemConfigurationFactory::class);
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
