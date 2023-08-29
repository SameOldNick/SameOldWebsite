<?php

namespace App\Rules;

use BladeUI\Icons\Exceptions\SvgNotFound;
use BladeUI\Icons\Factory;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\App;

class ValidBladeIcon implements ValidationRule
{
    protected $factory;

    public function __construct()
    {
        $this->factory = App::make(Factory::class);
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $this->factory->svg($value);
        } catch (SvgNotFound $e) {
            $fail('The specified icon does not exist.');
        }
    }
}
