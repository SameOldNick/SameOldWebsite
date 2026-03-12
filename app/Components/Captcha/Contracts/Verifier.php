<?php

namespace App\Components\Captcha\Contracts;

use App\Components\Captcha\Exceptions\ValidationException;
use App\Components\Captcha\Exceptions\VerificationException;

/**
 * @template TResponse of UserResponse
 */
interface Verifier
{
    /**
     * Verify the user response.
     *
     * @param  TResponse  $userResponse
     *
     * @throws VerificationException If the response is invalid.
     */
    public function verifyResponse(UserResponse $userResponse): void;

    /**
     * Validate the rule.
     *
     * @throws ValidationException If the rule is invalid.
     */
    public function validateRule(string $attribute, mixed $value): void;
}
