<?php

namespace App\Components\Captcha\Contracts;

interface Driver
{
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
