<?php

namespace App\Components\MFA;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use App\Components\MFA\Services\Authenticator\AuthenticatorService;
use App\Components\MFA\Services\Persist\PersistService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard as GuardContract;

class Guard implements GuardContract
{
    public function __construct(
        protected readonly GuardContract $guard,
        protected readonly AuthenticatorService $authenticator,
        protected readonly PersistService $persist
    ) {
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        $user = $this->user();

        if (is_null($user)) {
            return false;
        }

        if ($user instanceof MultiAuthenticatable) {
            if ($this->authenticator->isConfigured($user) && ! $this->persist->isVerified($user)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return ! $this->check();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function user()
    {
        return $this->getGuard()->user();
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|string|null
     */
    public function id()
    {
        return $this->getGuard()->id();
    }

    /**
     * Validate a user's credentials.
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return $this->getGuard()->hasUser();
    }

    /**
     * Determine if the guard has a user instance.
     *
     * @return bool
     */
    public function hasUser()
    {
        return $this->getGuard()->hasUser();
    }

    /**
     * Set the current user.
     *
     * @return void
     */
    public function setUser(Authenticatable $user)
    {
        $this->getGuard()->setUser($user);
    }

    /**
     * Gets the inner guard.
     */
    public function getGuard(): GuardContract
    {
        return $this->guard;
    }
}
