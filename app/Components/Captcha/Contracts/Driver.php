<?php

namespace App\Components\Captcha\Contracts;

interface Driver
{
    /**
     * Checks if the driver is ready to use.
     *
     * @return boolean
     */
    public function isReady(): bool;

    /**
     * Gets the presenter instance (for the front-end).
     *
     * @return Presenter
     */
    public function presenter(): Presenter;

    /**
     * Gets the verifier instance (for the back-end).
     *
     * @return Verifier
     */
    public function verifier(): Verifier;
}
