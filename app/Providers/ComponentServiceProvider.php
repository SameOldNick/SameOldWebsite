<?php

namespace App\Providers;

use App\Components\Compiler\ServiceProvider as CompilerServiceProvider;
use App\Components\LittleJWT\ServiceProvider as LittleJWTServiceProvider;
use App\Components\Macros\ServiceProvider as MacrosServiceProvider;
use App\Components\Menus\ServiceProvider as MenusServiceProvider;
use App\Components\OAuth\ServiceProvider as OAuthServiceProvider;
use App\Components\Settings\ServiceProvider as SettingsServiceProvider;
use App\Components\SweetAlert\ServiceProvider as SweetAlertServiceProvider;
use Illuminate\Support\AggregateServiceProvider;

class ComponentServiceProvider extends AggregateServiceProvider
{
    /**
     * The provider class names.
     *
     * @var array
     */
    protected $providers = [
        MenusServiceProvider::class,
        SweetAlertServiceProvider::class,
        MacrosServiceProvider::class,
        CompilerServiceProvider::class,
        LittleJWTServiceProvider::class,
        SettingsServiceProvider::class,
        OAuthServiceProvider::class,
    ];
}
