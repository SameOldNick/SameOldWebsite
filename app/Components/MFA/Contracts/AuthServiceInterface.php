<?php

namespace App\Components\MFA\Contracts;

use App\Components\MFA\Contracts\MultiAuthenticatable;

interface AuthServiceInterface {
    /**
     * Checks if MFA is configured for authenticatable.
     *
     * @param MultiAuthenticatable $authenticatable
     * @return boolean
     */
    public function isConfigured(MultiAuthenticatable $authenticatable): bool;

    /**
     * Verifies code for authenticatable.
     *
     * @param MultiAuthenticatable $authenticatable
     * @param string $code
     * @return boolean
     */
    public function verifyCode(MultiAuthenticatable $authenticatable, string $code): bool;
}
