<?php

namespace App\Components\Captcha;

use App\Components\Captcha\Contracts\Driver;
use App\Components\Captcha\Contracts\UserResponse;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;

class CaptchaService
{
    /**
     * The captcha manager instance.
     */
    public readonly CaptchaManager $manager;

    /**
     * Create a new captcha service instance.
     */
    public function __construct(
        private readonly Container $container,
    ) {
        $this->manager = new CaptchaManager($this->container);
    }

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
