<?php

namespace App\Components\MFA\Contracts;

use DateTimeInterface;
use Illuminate\Contracts\Auth\Authenticatable;

interface PersistServiceDriver
{
    /**
     * Checks if user is two-factor authenticated.
     */
    public function isVerified(Authenticatable $user): bool;

    /**
     * Marks user as two-factor authenticated.
     *
     * @return void
     */
    public function markVerified(Authenticatable $user, ?DateTimeInterface $expiry = null);

    /**
     * Clears user as two-factor authenticated.
     *
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
