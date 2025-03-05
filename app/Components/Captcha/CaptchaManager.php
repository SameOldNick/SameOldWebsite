<?php

namespace App\Components\Captcha;

use App\Components\Captcha\Contracts\Driver;
use App\Components\Captcha\Contracts\Providers\SettingsProvider;
use App\Components\Captcha\Drivers\Recaptcha\Driver as RecaptchaDriver;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;

final class CaptchaManager extends Manager
{
    public function __construct(
        Container $container,
        protected readonly SettingsProvider $settings
    ) {
        parent::__construct($container);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultDriver()
    {
        // Return the default driver name
        return $this->settings->defaultDriver();
    }

    /**
     * Create the Recaptcha driver.
     */
    protected function createRecaptchaDriver(): Driver
    {
        return new RecaptchaDriver($this->settings->get('recaptcha', default: []));
    }
}
