<?php

namespace App\Components\Captcha;

use App\Components\Captcha\Contracts\Driver;
use App\Components\Captcha\Contracts\UserResponse;

class CaptchaService
{
    /**
     * Create a new captcha service instance.
     */
    public function __construct(
        public readonly CaptchaManager $manager,
    ) {}

    /**
     * Validate the user response.
     *
     * @return void
     *
     * @throws \App\Components\Captcha\Exceptions\VerificationException Thrown if verification fails
     * @throws InvalidArgumentException Thrown if the user response is invalid
     */
    public function validate(UserResponse $userResponse)
    {
        $verifier = $this->getDriver($userResponse->getDriver())->verifier();

        $verifier->verifyResponse($userResponse);
    }

    /**
     * Get the captcha manager.
     */
    public function getManager(): CaptchaManager
    {
        return $this->manager;
    }

    /**
     * Get the captcha driver.
     */
    public function getDriver(?string $driver = null): Driver
    {
        return $this->getManager()->driver($driver);
    }
}
