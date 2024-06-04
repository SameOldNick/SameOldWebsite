<?php

namespace App\Components\Passwords\Contracts;

use App\Components\Passwords\Password;

interface Rule
{
    /**
     * Determines if rule is enabled.
     */
    public function isEnabled(): bool;

    /**
     * Attaches rule to Password instance
     */
    public function configure(Password $password): Password;
}
