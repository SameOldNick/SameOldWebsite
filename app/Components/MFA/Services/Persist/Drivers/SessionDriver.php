<?php

namespace App\Components\MFA\Services\Persist\Drivers;

use App\Components\MFA\Contracts\PersistServiceDriver;
use DateTimeInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Carbon;

class SessionDriver implements PersistServiceDriver
{
    public function __construct(
        protected readonly SessionManager $sessionManager,
        protected readonly array $config
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function isVerified(Authenticatable $user): bool
    {
        $key = $this->getSessionKey($user);

        if (! $this->sessionManager->has($key)) {
            return false;
        }

        $expires = Carbon::fromSerialized($this->sessionManager->get($key));

        if ($expires->isPast()) {
            $this->sessionManager->forget($key);

            return false;
        } else {
            return true;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function markVerified(Authenticatable $user, ?DateTimeInterface $expiry = null)
    {
        $this->sessionManager->put($this->getSessionKey($user), serialize($expiry ?? $this->getDefaultVerifiedExpiry()));
    }

    /**
     * {@inheritDoc}
     */
    public function clearVerified(Authenticatable $user)
    {
        $this->sessionManager->forget($this->getSessionKey($user));
    }

    /**
     * {@inheritDoc}
     */
    public function purge()
    {
    }

    /**
     * Gets session key
     *
     * @return string
     */
    protected function getSessionKey(Authenticatable $user)
    {
        return "tfa_verified_{$user->getAuthIdentifier()}";
    }

    /**
     * Gets default expiry for MFA.
     *
     * @return Carbon
     */
    protected function getDefaultVerifiedExpiry()
    {
        return $this->config['expiry'] > 0 ? Carbon::now()->addSeconds($this->config['expiry']) : Carbon::maxValue();
    }
}
