<?php

namespace App\Components\MFA;

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
            /** @var \Illuminate\Routing\Route $this */
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

            if ($options['otp']['enabled']) {
                $this->middleware(array_filter([
                    Arr::get($options, 'otp.redirect_if_authenticated.enabled', false) ? sprintf('%s:%s', RedirectIfAuthenticated::class, Arr::get($options, 'otp.redirect_if_authenticated.guard', null)) : null,
                    Arr::get($options, 'otp.throttle.enabled', false) ? ThrottleRequests::with(Arr::get($options, 'otp.throttle.max_attempts'), Arr::get($options, 'otp.throttle.decay_minutes'), Arr::get($options, 'otp.throttle.prefix')) : false,
                ]))->group(function () {
                    /** @var \Illuminate\Routing\Route $this */
                    $this->get('/auth/mfa', [AuthController::class, 'showMFAPrompt'])->name('auth.mfa');
                    $this->post('/auth/mfa', [AuthController::class, 'verifyMFACode'])->name('auth.mfa.verify');

                    $this->get('/auth/mfa/backup', [BackupController::class, 'showBackupCodePrompt'])->name('auth.mfa.backup');
                    $this->post('/auth/mfa/backup', [BackupController::class, 'verifyBackupCode'])->name('auth.mfa.backup.verify');
                });
            }
        };
    }
}
