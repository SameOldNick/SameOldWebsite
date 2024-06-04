<?php

namespace App\Components\Passwords\Contracts;

use SensitiveParameter;

interface Blacklist
{
    /**
     * Checks if value is blacklisted.
     *
     * @param  string  $value
     */
    public function isBlacklisted(#[SensitiveParameter] string $value): bool;
}
