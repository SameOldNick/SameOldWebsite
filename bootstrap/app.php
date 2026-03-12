<?php

use App\Components\MFA\Http\Middleware\MultiFactorAuthenticate;
use App\Components\SweetAlert\SweetAlertMiddleware;
use App\Http\Kernel;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Spatie\GoogleTagManager\GoogleTagManagerMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withSingletons([
        Illuminate\Contracts\Http\Kernel::class => Kernel::class,
        Illuminate\Contracts\Console\Kernel::class => App\Console\Kernel::class,
    ])->withMiddleware(function (Middleware $middleware) {
        $middleware->preventRequestsDuringMaintenance(except: [
            '/api/*',
            '/admin/*',
            '/user/*/avatar',
        ])->use([
            // \App\Http\Middleware\TrustHosts::class,
            TrustProxies::class,
            HandleCors::class,
            PreventRequestsDuringMaintenance::class,
            ValidatePostSize::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,
        ])->web([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            GoogleTagManagerMiddleware::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            SweetAlertMiddleware::class,
        ])->group('admin', [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            SubstituteBindings::class,
        ])->api([
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            SubstituteBindings::class,
        ])->alias([
            'auth' => Authenticate::class,
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'auth.session' => AuthenticateSession::class,
            'cache.headers' => SetCacheHeaders::class,
            'can' => Authorize::class,
            'guest' => RedirectIfAuthenticated::class,
            'password.confirm' => RequirePassword::class,
            'roles' => CheckRole::class,
            'signed' => ValidateSignature::class,
            'throttle' => ThrottleRequests::class,
            'verified' => EnsureEmailIsVerified::class,
            'auth.mfa' => MultiFactorAuthenticate::class,
        ]);
    })->withExceptions(function (Exceptions $exceptions) {
        //
    })->withEvents(false)->create();
