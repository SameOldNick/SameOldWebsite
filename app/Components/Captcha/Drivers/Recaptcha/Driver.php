<?php

namespace App\Components\Captcha\Drivers\Recaptcha;

use App\Components\Captcha\Contracts\Driver as DriverContract;
use App\Components\Captcha\Contracts\Presenter as PresenterContract;
use App\Components\Captcha\Contracts\Verifier as VerifierContract;

class Driver implements DriverContract
{
    /**
     * Constructs a new recaptcha driver.
     */
    public function __construct(
        protected readonly array $config
    ) {}

    /**
     * Gets the site key.
     */
    public function getSiteKey(): string
    {
        return $this->config['site_key'];
    }

    /**
     * Gets the secret key.
     */
    protected function getSecretKey(): string
    {
        return $this->config['secret_key'];
    }

    /**
     * Gets the minimum score.
     */
    public function getMinimumScore(): float
    {
        return $this->config['minimum_score'] ?? 0.5;
    }

    /**
     * Gets the client options (for the HTTP client).
     */
    public function getClientOptions(): array
    {
        return $this->config['client_options'] ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public function isReady(): bool
    {
        return ! empty($this->getSiteKey()) && ! empty($this->getSecretKey());
    }

    /**
     * {@inheritDoc}
     */
    public function presenter(): PresenterContract
    {
        return new Presenter($this->getSiteKey());
    }

    /**
     * {@inheritDoc}
     */
    public function verifier(): VerifierContract
    {
        return new Verifier($this->getSiteKey(), $this->getSecretKey(), $this->getMinimumScore(), $this->getClientOptions());
    }
}
