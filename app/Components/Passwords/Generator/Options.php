<?php

namespace App\Components\Passwords\Generator;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Immutable class representing generator options
 * @immutable
 */
final class Options implements Arrayable
{
    private static $createDefaultsCallback;

    public function __construct(
        public readonly int $min = 1,
        public readonly int $max = 26,
        public readonly int $uppercase = 0,
        public readonly int $lowercase = 0,
        public readonly int $numbers = 0,
        public readonly int $symbols = 0,
        public readonly bool $ascii = true,
        public readonly array $whitespaces = [
            'spaces' => 0,
            'tabs' => 0,
            'newlines' => 0,
        ],
    ) {
    }

    /**
     * Gets the bounds of the password
     *
     * @return list
     */
    public function getBounds()
    {
        return [
            $this->min,
            $this->max,
        ];
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray()
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
            'uppercase' => $this->uppercase,
            'lowercase' => $this->lowercase,
            'numbers' => $this->numbers,
            'symbols' => $this->symbols,
            'ascii' => $this->ascii,
            'whitespaces' => $this->whitespaces,
        ];
    }

    /**
     * Gets the default options
     *
     * @return static
     */
    public static function default(): static
    {
        if (is_callable(static::$createDefaultsCallback)) {
            return call_user_func(static::$createDefaultsCallback);
        } else {
            return new static;
        }
    }

    /**
     * Sets the default options
     *
     * @param callable|null $callback
     * @return void
     */
    public static function defaults(callable $callback = null): void
    {
        static::$createDefaultsCallback = $callback;
    }
}
