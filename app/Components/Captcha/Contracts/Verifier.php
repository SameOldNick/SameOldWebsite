<?php

namespace App\Components\Captcha\Contracts;

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
     * @throws \App\Components\Captcha\Exceptions\VerificationException If the response is invalid.
     */
    public function verifyResponse(UserResponse $userResponse): void;

    /**
     * Validate the rule.
     *
     * @throws \App\Components\Captcha\Exceptions\ValidationException If the rule is invalid.
     */
    public function validateRule(string $attribute, mixed $value): void;
}
