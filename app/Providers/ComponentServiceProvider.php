<?php

namespace App\Providers;

use App\Components\Analytics\ServiceProvider as AnalyticsServiceProvider;
use App\Components\Compiler\ServiceProvider as CompilerServiceProvider;
use App\Components\Encryption\ServiceProvider as EncryptionServiceProvider;
use App\Components\LittleJWT\ServiceProvider as LittleJWTServiceProvider;
use App\Components\Macros\ServiceProvider as MacrosServiceProvider;
use App\Components\Menus\ServiceProvider as MenusServiceProvider;
use App\Components\MFA\ServiceProvider as MFAServiceProvider;
use App\Components\OAuth\ServiceProvider as OAuthServiceProvider;
use App\Components\Security\ServiceProvider as SecurityServiceProvider;
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
        OAuthServiceProvider::class,
        AnalyticsServiceProvider::class,
        SecurityServiceProvider::class,
        EncryptionServiceProvider::class,
        MFAServiceProvider::class,
    ];
}
