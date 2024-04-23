<?php

namespace App\Components\Passwords\Rules;

use Closure;

class Whitespaces extends ValidationRule
{
    protected readonly mixed $spaces;

    protected readonly mixed $tabs;

    protected readonly mixed $newlines;

    /**
     * Initializes AllowWhitespaces instance
     *
     * @param mixed $spaces
     * @param mixed $tabs
     * @param mixed $newlines
     */
    public function __construct(...$args)
    {
        if (empty($args) || (count($args) === 1 && isset($args[0]))) {
            $this->spaces = $args[0] ?? false;
            $this->tabs = $args[0] ?? false;
            $this->newlines = $args[0] ?? false;
        } else {
            $this->spaces = $args['spaces'] ?? false;
            $this->tabs = $args['tabs'] ?? false;
            $this->newlines = $args['newlines'] ?? false;
        }
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function validate(string $attribute, #[\SensitiveParameter] mixed $value, Closure $fail)
    {
        if ($this->disallowAll()) {
            if (preg_match('/\s/', $value)) {
                $fail(__('The password can not have whitespace characters.'));
            }
        } else {
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
    }

    /**
     * Checks if all whitespaces are disallowed.
     *
     * @return bool
     */
    protected function disallowAll(): bool
    {
        return ! $this->spaces && ! $this->tabs && ! $this->newlines;
    }

    /**
     * Gets maximum value from boolean or int.
     *
     * @param bool|int $value
     * @return int
     */
    protected function max($value)
    {
        return $value === true ? PHP_INT_MAX : (int) $value;
    }
}
