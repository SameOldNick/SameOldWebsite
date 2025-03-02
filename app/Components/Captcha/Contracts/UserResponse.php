<?php

namespace App\Components\Captcha\Contracts;

interface UserResponse
{
    /**
     * Get the driver name.
     */
    public function getDriver(): string;
}
