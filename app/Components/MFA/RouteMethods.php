<?php

namespace App\Components\MFA;

use App\Components\MFA\Http\Controllers\OTPAuthController;
use App\Components\MFA\Http\Controllers\OTPBackupController;
use App\Http\Middleware\RedirectIfAuthenticated;

class RouteMethods {
    public function mfa() {
        return function ($options = []) {
            /** @var \Illuminate\Routing\Route $this */

            $defaults = [
                'otp' => [
                    'enabled' => true,
                ]
            ];

            $options = array_merge($defaults, $options);

            if ($options['otp']['enabled']) {
                $this->middleware(sprintf('%s:%s', RedirectIfAuthenticated::class, 'mfa'))->group(function() {
                    /** @var \Illuminate\Routing\Route $this */

                    $this->get('/auth/mfa', [OTPAuthController::class, 'showMFAPrompt'])->name('auth.mfa');
                    $this->post('/auth/mfa', [OTPAuthController::class, 'verifyMFACode'])->name('auth.mfa.verify');

                    $this->get('/auth/mfa/backup', [OTPBackupController::class, 'showBackupCodePrompt'])->name('auth.mfa.backup');
                    $this->post('/auth/mfa/backup', [OTPBackupController::class, 'verifyBackupCode'])->name('auth.mfa.backup.verify');
                });
            }
        };
    }
}
