<?php

namespace App\Components\MFA;

use App\Components\MFA\Facades\MFA;
use App\Components\MFA\Http\Controllers\OTP\AuthController;
use App\Components\MFA\Http\Controllers\OTP\BackupController;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Arr;

class RouteMethods
{
    public function mfa()
    {
        return function ($options = []) {
            /** @var \Illuminate\Routing\Router $this */
            $defaults = [
                'otp' => [
                    'enabled' => true,
                    'redirect_if_authenticated' => [
                        'enabled' => true,
                        'guard' => 'mfa',
                    ],
                    'throttle' => [
                        'enabled' => true,
                        'max_attempts' => 30,
                        'decay_minutes' => 1,
                        'prefix' => 'mfa',
                    ],
                ],
            ];

            $options = array_merge($defaults, $options);

            foreach (config('mfa.authenticator.routes', []) as $driver) {
                MFA::driver($driver)->registerRoutes($this, $options);
            }
        };
    }
}
