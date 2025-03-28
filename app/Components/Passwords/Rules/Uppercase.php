<?php

namespace App\Components\Passwords\Rules;

use Closure;

class Uppercase extends ValidationRule
{
    /**
     * {@inheritDoc}
     */
    public function validate(string $attribute, #[\SensitiveParameter] mixed $value, Closure $fail)
    {
        if (preg_match_all('/[A-Z]/', $value) < $this->value) {
            $fail(__('The password must have at least :count uppercase characters.', ['count' => $this->value]));
        }
    }
}
