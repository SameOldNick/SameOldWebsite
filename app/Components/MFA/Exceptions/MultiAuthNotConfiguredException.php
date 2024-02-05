<?php

namespace App\Components\MFA\Exceptions;

use Exception;

/**
 * Exception for when MFA is not configured.
 */
class MultiAuthNotConfiguredException extends Exception {
    /**
     * Initializes exception
     */
    public function __construct()
    {
        parent::__construct('Multi-Factor Authentication is not configured.');
    }

    /**
     * Throws the exception
     *
     * @return static
     */
    public static function throw(): static {
        throw new static();
    }
}
