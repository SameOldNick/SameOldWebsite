<?php

namespace App\Components\Passwords\Contracts;

use App\Components\Passwords\Password;

interface Rule {
    /**
     * Determines if rule is enabled.
     *
     * @return boolean
     */
    public function isEnabled(): bool;

    /**
     * Attaches rule to Password instance
     *
     * @param Password $password
     * @return Password
     */
    public function configure(Password $password): Password;
}
