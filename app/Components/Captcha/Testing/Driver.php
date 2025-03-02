<?php

namespace App\Components\Captcha\Testing;

use App\Components\Captcha\Contracts\Driver as DriverContract;
use App\Components\Captcha\Contracts\Presenter;
use App\Components\Captcha\Contracts\Verifier;

class Driver implements DriverContract
{
    /**
     * Creates a new testing driver instance.
     */
    public function __construct(
        protected readonly Presenter $presenter,
        protected readonly Verifier $verifier,
        protected readonly bool $ready = true
    ) {}

    /**
     * {@inheritDoc}
     */
    public function isReady(): bool
    {
        return $this->ready;
    }

    /**
     * {@inheritDoc}
     */
    public function presenter(): Presenter
    {
        return $this->presenter;
    }

    /**
     * {@inheritDoc}
     */
    public function verifier(): Verifier
    {
        return $this->verifier;
    }
}
