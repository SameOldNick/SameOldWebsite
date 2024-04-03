<?php

namespace App\Components\Passwords\Rules;

use App\Components\Passwords\Contracts\Rule;
use App\Components\Passwords\Password;
use Closure;

abstract class ValidationRule implements Rule {
    public function __construct(
        protected readonly mixed $value
    )
    {

    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool {
        return (bool) $this->value;
    }

    /**
     * Validates the value using the Laravel validator.
     *
     * @abstract
     * @param string $attribute Name of attribute
     * @param mixed $value Value to validate
     * @param Closure $fail Callback for when validation fails
     * @return void
     */
    public abstract function validate(string $attribute, #[\SensitiveParameter] mixed $value, Closure $fail);

    /**
     * Adds validation rule to Password instance.
     *
     * @param Password $password
     * @return Password
     */
    public function configure(Password $password): Password {
        return $password->addRule(fn ($attribute, $value, $fail) => $this->validate($attribute, $value, $fail));
    }
}
