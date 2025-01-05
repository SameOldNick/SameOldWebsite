<?php

namespace App\Rules;

use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

use function Safe\preg_match;

class RegexPattern implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            preg_match($value, '');
        } catch (Exception $ex) {
            $fail('The :attribute must be a RegEx pattern.');
        }
    }
}
