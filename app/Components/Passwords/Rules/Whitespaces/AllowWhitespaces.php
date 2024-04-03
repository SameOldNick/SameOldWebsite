<?php

namespace App\Components\Passwords\Rules\Whitespaces;

use Closure;
use App\Components\Passwords\Rules\ValidationRule;

class AllowWhitespaces extends ValidationRule {
    /**
     * Initializes AllowWhitespaces instance
     *
     * @param mixed $spaces
     * @param mixed $tabs
     * @param mixed $newlines
     */
    public function __construct(
        protected readonly mixed $spaces = false,
        protected readonly mixed $tabs = false,
        protected readonly mixed $newlines = false,
    )
    {

    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function validate(string $attribute, #[\SensitiveParameter] mixed $value, Closure $fail) {
        if (preg_match_all('/[ ]/', $value) > $this->max($this->spaces)) {
            $fail(__('The password must have no more than :count spaces.', ['count' => $this->max($this->spaces)]));
        }

        if (preg_match_all('/\t/', $value) > $this->max($this->tabs)) {
            $fail(__('The password must have no more than :count tabs.', ['count' => $this->max($this->tabs)]));
        }

        if (preg_match_all('/(\r\n|\n\r|\r|\n)/', $value) > $this->max($this->newlines)) {
            $fail(__('The password must have no more than :count newlines.', ['count' => $this->max($this->newlines)]));
        }
    }

    /**
     * Gets maximum value from boolean or int.
     *
     * @param bool|int $value
     * @return int
     */
    protected function max($value) {
        return $value === true ? PHP_INT_MAX : (int) $value;
    }
}
