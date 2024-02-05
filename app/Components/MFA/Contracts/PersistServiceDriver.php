<?php

namespace App\Components\MFA\Contracts;

use DateTimeInterface;
use Illuminate\Contracts\Auth\Authenticatable;

interface PersistServiceDriver {
    /**
     * Checks if user is two-factor authenticated.
     *
     * @param Authenticatable $user
     * @return boolean
     */
    public function isVerified(Authenticatable $user): bool;

    /**
     * Marks user as two-factor authenticated.
     *
     * @param Authenticatable $user
     * @param DateTimeInterface|null $expiry
     * @return void
     */
    public function markVerified(Authenticatable $user, DateTimeInterface $expiry = null);

    /**
     * Clears user as two-factor authenticated.
     *
     * @param Authenticatable $user
     * @return void
     */
    public function clearVerified(Authenticatable $user);

    /**
     * Purges two-factor authentication sessions.
     *
     * @return void
     */
    public function purge();
}
