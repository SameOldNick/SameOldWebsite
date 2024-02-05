<?php

namespace App\Http\Controllers\Main\User;

use App\Components\MFA\Rules\CurrentAuthCode;
use App\Components\MFA\Services\Authenticator\AuthenticatorService;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\OneTimeAuthenticatable;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\Setup\Backup;
use App\Components\MFA\Services\Persist\PersistService;
use App\Http\Controllers\Controller;
use App\Http\Middleware\MFASetupInitialized;
use Illuminate\Http\Request;

class MFASetupController extends Controller
{
    public function __construct(
        protected readonly AuthenticatorService $authenticatorService,
        protected readonly PersistService $persistService
    ) {
        $this->middleware(MFASetupInitialized::class)->except('confirmPassword');
    }

    /**
     * Confirms the user entered the current password.
     *
     * @param Request $request
     * @return void
     */
    public function confirmPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $request->session()->put('mfa_secret', OneTimeAuthenticatable::generate()->resolveSecret());

        // Redirect to show install steps
        return redirect()->action([static::class, 'showInstallationInstructions']);
    }

    /**
     * Displays the steps to install the MFA secret and a form to confirm the code.
     *
     * @param Request $request
     * @return void
     */
    public function showInstallationInstructions(Request $request)
    {
        $setupConfig = $this->authenticatorService->setup($request->user(), $request->session()->get('mfa_secret'));

        return view('main.user.tfa.install', $setupConfig->getConfiguration());
    }

    /**
     * Confirms the user entered the correct MFA code.
     *
     * @param Request $request
     * @return void
     */
    public function confirmMFA(Request $request)
    {
        $request->validate([
            'code' => ['required', new CurrentAuthCode(OneTimeAuthenticatable::string($request->session()->get('mfa_secret')))],
        ]);

        // Redirect to show backup codes
        return redirect()->action([static::class, 'showBackupCodes']);
    }

    /**
     * Displays the backup codes to the user.
     *
     * @param Request $request
     * @return void
     */
    public function showBackupCodes(Request $request)
    {
        $setupConfig = $this->authenticatorService->setup($request->user(), $request->session()->get('mfa_secret'));

        if (! $request->session()->has('mfa_backup_secret')) {
            $request->session()->put('mfa_backup_secret', OneTimeAuthenticatable::generate()->resolveSecret());
        }

        $codes = $this->authenticatorService->driver('backup')->getCodes($request->session()->get('mfa_backup_secret'));

        return view('main.user.tfa.backup', [
            'codes' => $codes,
            'backupSecret' => $request->session()->get('mfa_backup_secret'),
        ] + $setupConfig->getConfiguration());
    }

    /**
     * Acknowledges the backup codes were stored.
     *
     * @param Request $request
     * @return void
     */
    public function acknowledgeBackupCodes(Request $request)
    {
        $request->validate(
            ['stored' => 'accepted'],
            ['stored.accepted' => 'You must acknowledge you have stored the backup codes securely.']
        );

        // Redirect to completion page.
        return redirect()->action([static::class, 'completeSetup']);
    }

    /**
     * Displays page confirming MFA is installed.
     *
     * @param Request $request
     * @return void
     */
    public function completeSetup(Request $request)
    {
        $this->authenticatorService->install($request->user(), $request->session()->get('mfa_secret'), $request->session()->get('mfa_backup_secret'));

        $request->session()->forget(['mfa_secret', 'mfa_backup_secret']);

        // Set as MFA verified so user doesn't have to enter code again.
        $this->persistService->markVerified($request->user());

        return view('main.user.tfa.finish');
    }
}
