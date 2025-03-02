<?php

namespace App\Components\Captcha\Contracts;

interface UserResponse
{
    /**
     * Get the driver name.
     *
     * @return string
     */
    public function getDriver(): string;
}
