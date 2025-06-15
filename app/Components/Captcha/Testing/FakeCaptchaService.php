<?php

namespace App\Components\Captcha\Testing;

use App\Components\Captcha\CaptchaManager;
use App\Components\Captcha\CaptchaService;
use App\Components\Captcha\Contracts\Driver;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Testing\Fakes\Fake;

class FakeCaptchaService extends CaptchaService implements Fake
{
    /**
     * Creates a new fake captcha service instance.
     */
    public function __construct(
        CaptchaManager $manager,
        protected readonly array $drivers,
    ) {
        parent::__construct($manager);
    }

    /**
     * {@inheritDoc}
     */
    public function getDriver(?string $driver = null): Driver
    {
        $driver = $driver ?? $this->getManager()->getDefaultDriver();

        return match (true) {
            isset($this->drivers[$driver]) => $this->drivers[$driver],
            isset($this->drivers['*']) => $this->drivers['*'],
            default => parent::getDriver($driver),
        };
    }
}
