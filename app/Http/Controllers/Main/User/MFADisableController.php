<?php

namespace App\Http\Controllers\Main\User;

use App\Components\MFA\Concerns\UsesMultiFactorAuthenticator;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\OneTimePasscode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\OneTimeAuthenticatable;
use Illuminate\Support\Facades\Session;
use App\Components\MFA\Rules\CurrentAuthCode;
use App\Components\MFA\Services\Authenticator\AuthenticatorService;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\Setup\Backup;

class MFADisableController extends Controller
{
    /**
     * Confirms the user entered the current password.
     *
     * @param Request $request
     * @return void
     */
    public function disableMFA(Request $request, AuthenticatorService $authenticatorService)
    {
        $request->validate([
            'password' => 'required|current_password',
            'current_otp' => ['required', new CurrentAuthCode]
        ]);

        $authenticatorService->uninstall($request->user());

        // Redirect to show install steps
        return redirect()->route('user.security')->with('success', __('MFA has been disabled.'));
    }
}
