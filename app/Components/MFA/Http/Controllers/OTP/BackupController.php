<?php

namespace App\Components\MFA\Http\Controllers\OTP;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Events\OTP\BackupCodeVerified;
use App\Components\MFA\Facades\MFA;
use App\Components\MFA\Rules\CurrentAuthCode;
use App\Components\MFA\Services\Authenticator\AuthenticatorService;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\OneTimeAuthenticatable;
use App\Components\MFA\Services\Persist\PersistService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function __construct(
        protected readonly AuthenticatorService $authenticatorService,
        protected readonly PersistService $persistService
    ) {
    }

    /**
     * Shows backup code prompt.
     *
     * @param Request $request
     * @return mixed
     */
    public function showBackupCodePrompt(Request $request)
    {
        return view('mfa::otp.backup');
    }

    /**
     * Verifies backup code.
     *
     * @param Request $request
     * @param AuthenticatorService $authService
     * @return mixed
     */
    public function verifyBackupCode(Request $request, AuthenticatorService $authService)
    {
        $request->validate([
            'code' => [
                'required',
                new CurrentAuthCode(OneTimeAuthenticatable::backup($this->getAuthenticatable($request)), $authService->driver('backup')),
            ],
        ]);

        $this->verifiedBackupCode($request);

        return $this->verifiedBackupCodeResponse($request);
    }

    /**
     * Handles verified backup code
     *
     * @param Request $request
     * @return void
     */
    protected function verifiedBackupCode(Request $request)
    {
        $authenticatable = $this->getAuthenticatable($request);

        // Fire backup code verified event
        BackupCodeVerified::dispatch($authenticatable);

        // Disable MFA for user
        $this->authenticatorService->uninstall($authenticatable);
    }

    /**
     * Creates response for when backup code is verified.
     *
     * @param Request $request
     * @return mixed
     */
    protected function verifiedBackupCodeResponse(Request $request)
    {
        return redirect()->to($this->getIntendedUrl($request));
    }

    /**
     * Gets the authenticatable subject.
     *
     * @param Request $request
     * @return MultiAuthenticatable
     */
    protected function getAuthenticatable(Request $request): MultiAuthenticatable
    {
        return $request->user();
    }

    /**
     * Gets the intended URL to redirect to.
     *
     * @param Request $request
     * @return string
     */
    protected function getIntendedUrl(Request $request): string
    {
        return redirect()->getIntendedUrl() ?? route('user.profile');
    }
}
