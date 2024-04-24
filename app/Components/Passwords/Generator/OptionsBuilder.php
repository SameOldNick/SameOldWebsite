<?php

namespace App\Components\Passwords\Generator;

use App\Components\Passwords\Concerns\GeneratesFromPasswordRules;

/**
 * Builds options for generator
 */
final class OptionsBuilder
{
    use GeneratesFromPasswordRules;

    protected $min;

    protected $max;

    protected $chars;

    protected $whitespaces;

    /**
     * Initializes OptionsBuilder
     */
    public function __construct()
    {
        $this->min = 0;
        $this->max = 0;

        $this->chars = [
            'uppercase' => 0,
            'lowercase' => 0,
            'numbers' => 0,
            'symbols' => 0,
            'ascii' => true,
        ];

        $this->whitespaces = [
            'spaces' => 0,
            'tabs' => 0,
            'newlines' => 0,
        ];
    }

    /**
     * Sets the minimum length
     *
     * @param int $length Minimum length (0 means default limit)
     * @return $this
     */
    public function minimumLength(int $length)
    {
        $this->min = $length;

        return $this;
    }

    /**
     * Sets the maximum length
     *
     * @param int $length Maximum length (0 means default limit)
     * @return $this
     */
    public function maximumLength(int $length)
    {
        $this->max = $length;

        return $this;
    }

    /**
     * Sets the number of required lowercase characters
     *
     * @param int $count Required count (0 means no requirement)
     * @return $this
     */
    public function requiresLowerCase(int $count)
    {
        $this->chars['lowercase'] = $count;

        return $this;
    }

    /**
     * Sets the number of required uppercase characters
     *
     * @param int $count Required count (0 means no requirement)
     * @return $this
     */
    public function requiresUpperCase(int $count)
    {
        $this->chars['uppercase'] = $count;

        return $this;
    }

    /**
     * Sets the number of required numbers
     *
     * @param int $count Required count (0 means no requirement)
     * @return $this
     */
    public function requiresNumbers(int $count)
    {
        $this->chars['numbers'] = $count;

        return $this;
    }

    /**
     * Sets the number of required special symbols
     *
     * @param int $count Required count (0 means no requirement)
     * @return $this
     */
    public function requiresSpecialSymbols(int $count)
    {
        $this->chars['symbols'] = $count;

        return $this;
    }

    /**
     * Sets if only 7-bit ASCII characters are allowed.
     *
     * @param bool $value
     * @return $this
     */
    public function onlyAscii($value)
    {
        $this->chars['ascii'] = (bool) $value;

        return $this;
    }

    /**
     * Sets if whitespaces are allowed.
     *
     * @param bool|array $value
     * @return $this
     */
    public function whitespaces($value)
    {
        if (is_bool($value)) {
            $this->whitespaces = [
                'spaces' => $value ? PHP_INT_MAX : 0,
                'tabs' => $value ? PHP_INT_MAX : 0,
                'newlines' => $value ? PHP_INT_MAX : 0,
            ];
        } elseif (is_array($value)) {
            $this->whitespaces = [
                'spaces' => (int) $value['spaces'] ?? 0,
                'tabs' => (int) $value['tabs'] ?? 0,
                'newlines' => (int) $value['newlines'] ?? 0,
            ];
        }

        return $this;
    }

    /**
     * Gets options for generator
     *
     * @return Options
     */
    public function getOptions(): Options
    {
        return new Options(...$this->getOptionsArgs());
    }

    /**
     * Gets args to use to build Options instance
     *
     * @return array
     */
    protected function getOptionsArgs()
    {
        $args = [];

        if ($this->min > 0) {
            $args['min'] = $this->min;
        }

        if ($this->max > 0) {
            $args['max'] = $this->max;
        }

        if ($this->chars['uppercase'] > 0) {
            $args['uppercase'] = $this->chars['uppercase'];
        }

        if ($this->chars['lowercase'] > 0) {
            $args['lowercase'] = $this->chars['lowercase'];
        }

        if ($this->chars['numbers'] > 0) {
            $args['numbers'] = $this->chars['numbers'];
        }

        if ($this->chars['symbols'] > 0) {
            $args['symbols'] = $this->chars['symbols'];
        }

        $args['ascii'] = $this->chars['ascii'];
        $args['whitespaces'] = $this->whitespaces;

        return $args;
    }
}
