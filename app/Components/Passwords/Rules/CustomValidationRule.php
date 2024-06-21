<?php

namespace App\Components\Passwords\Rules;

use Closure;

class CustomValidationRule extends ValidationRule
{
    /**
     * Initializes CustomValidationRule instance
     */
    public function __construct(
        protected readonly Closure $callback
    ) {}

    /**
     * {@inheritDoc}
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function validate(string $attribute, #[\SensitiveParameter] mixed $value, Closure $fail)
    {
        call_user_func($this->callback, [$attribute, $value, $fail]);
    }
}
