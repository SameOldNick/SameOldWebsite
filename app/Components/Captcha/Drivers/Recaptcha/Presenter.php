<?php

namespace App\Components\Captcha\Drivers\Recaptcha;

use App\Components\Captcha\Contracts\Presenter as PresenterContract;

class Presenter implements PresenterContract
{
    const RECAPTCHA_JS_URL = 'https://www.google.com/recaptcha/api.js';

    /**
     * Constructs a new instance of the presenter.
     */
    public function __construct(
        private readonly string $siteKey
    ) {}

    /**
     * Gets the reCAPTCHA site key.
     */
    public function getSiteKey(): string
    {
        return $this->siteKey;
    }

    /**
     * Gets the reCAPTCHA JS URL.
     */
    public function getJsUrl(): string
    {
        return self::RECAPTCHA_JS_URL;
    }

    /**
     * Get the script source URL.
     */
    public function scriptSrc(): string
    {
        return $this->getJsUrl().'?render='.$this->getSiteKey();
    }

    /**
     * {@inheritDoc}
     */
    public function render(array $attributes, array $data)
    {
        return view('captcha::recaptcha.script', [
            'jsUrl' => $this->getJsUrl(),
            'siteKey' => $this->getSiteKey(),
            'jsCallback' => $attributes['jsCallback'] ?? false,
            'jsCallerName' => $attributes['jsCallerName'] ?? 'prepareRecaptcha',
            'jsAutoCall' => $attributes['jsAutoCall'] ?? false,
        ]);
    }
}
