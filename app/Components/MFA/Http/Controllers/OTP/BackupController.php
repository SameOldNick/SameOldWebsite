<?php

namespace App\Components\MFA\Http\Controllers\OTP;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Contracts\SecretStore;
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
    ) {}

    /**
     * Shows backup code prompt.
     *
     * @return mixed
     */
    public function showBackupCodePrompt(Request $request)
    {
        return view('mfa::otp.backup');
    }

    /**
     * Verifies backup code.
     *
     * @return mixed
     */
    public function verifyBackupCode(Request $request, AuthenticatorService $authService, SecretStore $secretStore)
    {
        $request->validate([
            'code' => [
                'required',
                new CurrentAuthCode(
                    OneTimeAuthenticatable::string($secretStore->getBackupSecret($this->getAuthenticatable($request))),
                    $authService->driver('backup')
                ),
            ],
        ]);

        $this->verifiedBackupCode($request);

        return $this->verifiedBackupCodeResponse($request);
    }

    /**
     * Handles verified backup code
     *
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
     * @return mixed
     */
    protected function verifiedBackupCodeResponse(Request $request)
    {
        // TODO: Notify user their MFA is disabled.

        return redirect()->to($this->getIntendedUrl($request));
    }

    /**
     * Gets the authenticatable subject.
     */
    protected function getAuthenticatable(Request $request): MultiAuthenticatable
    {
        return $request->user();
    }

    /**
     * Gets the intended URL to redirect to.
     */
    protected function getIntendedUrl(Request $request): string
    {
        return redirect()->getIntendedUrl() ?? route('user.profile');
    }
}
