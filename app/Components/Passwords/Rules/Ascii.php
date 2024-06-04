<?php

namespace App\Components\Passwords\Rules;

use Closure;
use Illuminate\Support\Str;

class Ascii extends ValidationRule
{
    /**
     * {@inheritDoc}
     */
    public function validate(string $attribute, #[\SensitiveParameter] mixed $value, Closure $fail)
    {
        if (! Str::isAscii($value)) {
            $fail(__('The password can only have ASCII characters.'));
        }
    }
}
