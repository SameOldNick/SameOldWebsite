<?php

namespace App\Components\Passwords\Rules;

use Closure;

class SpecialSymbols extends ValidationRule {
    /**
     * @inheritDoc
     */
    public function validate(string $attribute, #[\SensitiveParameter] mixed $value, Closure $fail) {
        if (preg_match_all('/[\x21-\x2F\x3A-\x40\x5B-\x60\x7B-\x7E]/', $value) < $this->value) {
            $fail(__('The password must have at least :count special symbols.', ['count' => $this->value]));
        }
    }
}
