<?php

namespace App\Components\Backup;

use App\Components\Backup\DbDumper\MySqlPHP;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Spatie\Backup\Tasks\Backup\DbDumperFactory;

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
        DbDumperFactory::extend('mysql', function () {
            return new MySqlPHP;
        });
    }
}
