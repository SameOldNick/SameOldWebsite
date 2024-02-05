<?php

namespace App\Components\MFA\Http\Middleware;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Exceptions\MFARequiredException;
use App\Components\MFA\Services\Authenticator\AuthenticatorService;
use App\Components\MFA\Services\Persist\PersistService;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class MultiFactorAuthenticate
{
    public function __construct(
        protected readonly AuthenticatorService $authenticator,
        protected readonly PersistService $persist
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param Request  $request
     * @param  Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $this->isAuthenticated($request)) {
            return $this->unauthenticated($request);
        }

        if (! $this->isMultiFactorAuthenticated($request)) {
            return $this->notMultiFactorAuthenticated($request);
        }

        return $next($request);
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
     * Checks if user is authenticated (not multi-factor authenticated)
     *
     * @param Request $request
     * @return bool
     */
    protected function isAuthenticated(Request $request): bool
    {
        return ! is_null($this->getAuthenticatable($request));
    }

    /**
     * Creates response for unauthenticated user.
     *
     * @param Request $request
     * @return mixed
     */
    protected function unauthenticated(Request $request)
    {
        throw new AuthenticationException(
            'Unauthenticated.', [], $this->redirectToAuthenticate($request)
        );
    }

    /**
     * Where to redirect user for authentication.
     *
     * @param Request $request
     * @return string
     */
    protected function redirectToAuthenticate(Request $request)
    {
        return route('login');
    }

    /**
     * Checks if user is multi-factor authenticated.
     *
     * @param Request $request
     * @return bool
     */
    protected function isMultiFactorAuthenticated(Request $request): bool
    {
        $authenticatable = $this->getAuthenticatable($request);

        if ($this->authenticator->isConfigured($authenticatable) && ! $this->persist->isVerified($authenticatable)) {
            return false;
        }

        return true;
    }

    /**
     * Creates response for user not multi-factor authenticated.
     *
     * @param Request $request
     * @return mixed
     */
    protected function notMultiFactorAuthenticated(Request $request)
    {
        MFARequiredException::throw($this->redirectToMFA($request));
    }

    /**
     * Where to user for multi-factor authentication.
     *
     * @param Request $request
     * @return string
     */
    protected function redirectToMFA(Request $request)
    {
        return route('auth.mfa');
    }
}
