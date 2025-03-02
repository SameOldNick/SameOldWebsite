<?php

namespace App\Components\Captcha\Drivers\Recaptcha;

use App\Components\Captcha\Contracts\UserResponse as UserResponseContract;

class UserResponse implements UserResponseContract
{
    /**
     * Generates a new user response instance.
     *
     * @param string $response
     * @param string|null $remoteIp
     */
    public function __construct(
        public readonly string $response,
        public readonly ?string $remoteIp,
    ) {}

    /**
     * @inheritDoc
     */
    public function getDriver(): string
    {
        return 'recaptcha';
    }
}
