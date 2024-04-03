<?php

namespace App\Components\Passwords\Rules;

use Closure;

class Numbers extends ValidationRule {
    /**
     * @inheritDoc
     */
    public function validate(string $attribute, #[\SensitiveParameter] mixed $value, Closure $fail) {
        if (preg_match_all('/[0-9]/', $value) < $this->value) {
            $fail(__('The password must have at least :count numbers.', ['count' => $this->value]));
        }
    }
}
