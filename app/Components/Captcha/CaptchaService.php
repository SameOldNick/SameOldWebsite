<?php

namespace App\Components\Captcha;

use App\Components\Captcha\Contracts\Adapter;
use App\Components\Captcha\Contracts\Driver;
use App\Components\Captcha\Contracts\UserResponse;
use App\Components\Captcha\Contracts\Verifier;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;

class CaptchaService
{
    /**
     * The captcha manager instance.
     *
     * @var CaptchaManager
     */
    public readonly CaptchaManager $manager;

    /**
     * Create a new captcha service instance.
     *
     * @param Container $container
     */
    public function __construct(
        private readonly Container $container,
    ) {
        $this->manager = new CaptchaManager($this->container);
    }

    /**
     * Validate the user response.
     *
     * @param UserResponse $userResponse
     * @return void
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
     *
     * @return CaptchaManager
     */
    public function getManager(): CaptchaManager
    {
        return $this->manager;
    }

    /**
     * Get the captcha driver.
     *
     * @param string|null $driver
     * @return Driver
     */
    public function getDriver(?string $driver = null): Driver
    {
        return $this->getManager()->driver($driver);
    }
}
