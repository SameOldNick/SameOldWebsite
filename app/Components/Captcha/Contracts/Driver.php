<?php

namespace App\Components\Captcha\Contracts;

interface Driver
{
    /**
     * Checks if the driver is ready to use.
     */
    public function isReady(): bool;

    /**
     * Gets the presenter instance (for the front-end).
     */
    public function presenter(): Presenter;

    /**
     * Gets the verifier instance (for the back-end).
     */
    public function verifier(): Verifier;
}
