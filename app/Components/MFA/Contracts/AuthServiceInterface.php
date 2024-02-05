<?php

namespace App\Components\MFA\Contracts;

interface AuthServiceInterface
{
    /**
     * Checks if MFA is configured for authenticatable.
     *
     * @param MultiAuthenticatable $authenticatable
     * @return bool
     */
    public function isConfigured(MultiAuthenticatable $authenticatable): bool;

    /**
     * Verifies code for authenticatable.
     *
     * @param MultiAuthenticatable $authenticatable
     * @param string $code
     * @return bool
     */
    public function verifyCode(MultiAuthenticatable $authenticatable, string $code): bool;
}
