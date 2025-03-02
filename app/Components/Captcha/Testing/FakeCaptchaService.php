<?php

namespace App\Components\Captcha\Testing;

use App\Components\Captcha\CaptchaService;
use App\Components\Captcha\Contracts\Adapter;
use App\Components\Captcha\Contracts\Driver;
use App\Components\Captcha\Contracts\UserResponse;
use App\Components\Captcha\Contracts\Verifier;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;
use Illuminate\Support\Testing\Fakes\Fake;

class FakeCaptchaService extends CaptchaService implements Fake
{
    /**
     * Creates a new fake captcha service instance.
     *
     * @param Container $container
     * @param Driver|null $driver
     */
    public function __construct(
        Container $container,
        protected readonly ?Driver $driver,
    ) {
        parent::__construct($container);
    }

    /**
     * @inheritDoc
     */
    public function getDriver(?string $driver = null): Driver
    {
        return $this->driver ?? parent::getDriver($driver);
    }
}
