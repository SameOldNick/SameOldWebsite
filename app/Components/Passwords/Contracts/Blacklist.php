<?php

namespace App\Components\Passwords\Contracts;

use SensitiveParameter;

interface Blacklist
{
    /**
     * Checks if value is blacklisted.
     */
    public function isBlacklisted(#[SensitiveParameter] string $value): bool;
}
