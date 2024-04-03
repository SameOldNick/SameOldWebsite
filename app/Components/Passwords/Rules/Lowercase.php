<?php

namespace App\Components\Passwords\Rules;

use Closure;

class Lowercase extends ValidationRule {
    /**
     * @inheritDoc
     */
    public function validate(string $attribute, #[\SensitiveParameter] mixed $value, Closure $fail) {
        if (preg_match_all('/[a-z]/', $value) < $this->value) {
            $fail(__('The password must have at least :count lowercase characters.', ['count' => $this->value]));
        }
    }
}
