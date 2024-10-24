<?php

namespace App\Rules;

use Closure;
use Cron\CronExpression as CronCronExpression;
use Illuminate\Contracts\Validation\ValidationRule;

class CronExpression implements ValidationRule
{
    public function __construct() {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! CronCronExpression::isValidExpression($value)) {
            $fail('The :attribute value is not a valid Cron expression.');
        }
    }
}
