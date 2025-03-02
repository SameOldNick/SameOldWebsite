<?php

namespace App\Components\Captcha;

use App\Components\Captcha\Contracts\Driver;
use App\Components\Captcha\Drivers\Recaptcha\Driver as RecaptchaDriver;
use Illuminate\Support\Manager;

final class CaptchaManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function getDefaultDriver()
    {
        // Return the default driver name
        return $this->config->get('captcha.default');
    }

    /**
     * Create the Recaptcha driver.
     *
     * @return Driver
     */
    protected function createRecaptchaDriver(): Driver
    {
        return new RecaptchaDriver($this->config->get('captcha.drivers.recaptcha', []));
    }
}
