<?php

namespace App\Components\Passwords\Rules\Whitespaces;

use Closure;
use App\Components\Passwords\Rules\ValidationRule;

class DenyWhitespaces extends ValidationRule {
    /**
     * @inheritDoc
     */
    public function validate(string $attribute, #[\SensitiveParameter] mixed $value, Closure $fail) {
        if (preg_match('/\s/', $value)) {
            $fail(__('The password can not have whitespace characters.'));
        }
    }
}
