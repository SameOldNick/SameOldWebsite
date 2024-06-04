<?php

namespace App\Components\Passwords\Rules;

use App\Components\Passwords\Contracts\Rule;
use App\Components\Passwords\Password;

class MaxLength implements Rule
{
    public function __construct(
        public readonly int $max
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled(): bool
    {
        return $this->max > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Password $password): Password
    {
        return $password->max($this->max);
    }
}
