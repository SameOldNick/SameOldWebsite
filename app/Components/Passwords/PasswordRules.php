<?php

namespace App\Components\Passwords;

use App\Components\Passwords\Contracts\Rule;

final class PasswordRules
{
    /**
     * Creates instance of PasswordRules
     */
    public function __construct(
        private readonly array $rules
    ) {
    }

    /**
     * Gets the password rules.
     *
     * @return Rule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
