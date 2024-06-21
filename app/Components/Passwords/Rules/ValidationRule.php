<?php

namespace App\Components\Passwords\Rules;

use App\Components\Passwords\Contracts\Rule;
use App\Components\Passwords\Password;
use Closure;

abstract class ValidationRule implements Rule
{
    public function __construct(
        public readonly mixed $value
    ) {}

    /**
     * {@inheritDoc}
     */
    public function isEnabled(): bool
    {
        return (bool) $this->value;
    }

    /**
     * Validates the value using the Laravel validator.
     *
     * @param  string  $attribute  Name of attribute
     * @param  mixed  $value  Value to validate
     * @param  Closure  $fail  Callback for when validation fails
     * @return void
     */
    abstract public function validate(string $attribute, #[\SensitiveParameter] mixed $value, Closure $fail);

    /**
     * Adds validation rule to Password instance.
     */
    public function configure(Password $password): Password
    {
        return $password->addRule(fn ($attribute, $value, $fail) => $this->validate($attribute, $value, $fail));
    }
}
