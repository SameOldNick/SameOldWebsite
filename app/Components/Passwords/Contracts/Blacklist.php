<?php

namespace App\Components\Passwords\Contracts;

use SensitiveParameter;

interface Blacklist {
    /**
     * Checks if value is blacklisted.
     *
     * @param string $value
     * @return bool
     */
    public function isBlacklisted(#[SensitiveParameter] string $value): bool;
}
