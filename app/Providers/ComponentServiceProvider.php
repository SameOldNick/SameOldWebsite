<?php

namespace App\Providers;

use App\Components\Analytics\ServiceProvider as AnalyticsServiceProvider;
use App\Components\Backup\ServiceProvider as BackupServiceProvider;
use App\Components\Captcha\ServiceProvider as CaptchaServiceProvider;
use App\Components\Compiler\ServiceProvider as CompilerServiceProvider;
use App\Components\Dusk\ServiceProvider as DuskServiceProvider;
use App\Components\Encryption\ServiceProvider as EncryptionServiceProvider;
use App\Components\Fakers\ServiceProvider as FakersServiceProvider;
use App\Components\LittleJWT\ServiceProvider as LittleJWTServiceProvider;
use App\Components\Macros\ServiceProvider as MacrosServiceProvider;
use App\Components\Menus\ServiceProvider as MenusServiceProvider;
use App\Components\MFA\ServiceProvider as MFAServiceProvider;
use App\Components\Moderator\ServiceProvider as ModeratorServiceProvider;
use App\Components\OAuth\ServiceProvider as OAuthServiceProvider;
use App\Components\Passwords\ServiceProvider as PasswordsServiceProvider;
use App\Components\Search\ServiceProvider as SearchServiceProvider;
use App\Components\Security\ServiceProvider as SecurityServiceProvider;
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
        OAuthServiceProvider::class,
        AnalyticsServiceProvider::class,
        SecurityServiceProvider::class,
        EncryptionServiceProvider::class,
        MFAServiceProvider::class,
        FakersServiceProvider::class,
        PasswordsServiceProvider::class,
        BackupServiceProvider::class,
        ModeratorServiceProvider::class,
        SettingsServiceProvider::class,
        DuskServiceProvider::class,
        SearchServiceProvider::class,
        CaptchaServiceProvider::class,
    ];
}
