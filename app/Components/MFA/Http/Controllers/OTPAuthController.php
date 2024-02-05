<?php

namespace App\Components\MFA\Http\Controllers;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Facades\MFA;
use App\Components\MFA\Rules\CurrentAuthCode;
use App\Components\MFA\Services\Authenticator\AuthenticatorService;
use App\Components\MFA\Services\Persist\PersistService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OTPAuthController extends Controller
{
    public function __construct(
        protected readonly AuthenticatorService $authenticatorService,
        protected readonly PersistService $persistService
    ) {
        // TODO: Add rate limiting
    }

    /**
     * Displays prompt for MFA code.
     *
     * @param Request $request
     * @return mixed
     */
    public function showMFAPrompt(Request $request)
    {
        return view('mfa::otp.prompt');
    }

    /**
     * Verifies MFA code.
     *
     * @param Request $request
     * @return mixed
     */
    public function verifyMFACode(Request $request)
    {
        $request->validate([
            'code' => [
                'required',
                new CurrentAuthCode($this->getAuthenticatable($request)),
            ],
        ]);

        $this->verifiedMFACodeAuth($request);

        return $this->verifiedAuthResponse($request);
    }

    /**
     * Handles verified MFA code.
     *
     * @param Request $request
     * @return void
     */
    protected function verifiedMFACodeAuth(Request $request)
    {
        // TODO: Fire event that user is MFA

        $this->persistService->markVerified($this->getAuthenticatable($request));
    }

    /**
     * Creates response for when MFA code is verified.
     *
     * @param Request $request
     * @return mixed
     */
    protected function verifiedMFACodeResponse(Request $request)
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
