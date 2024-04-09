<?php

namespace App\Components\Passwords;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Password as LaravelPassword;
use InvalidArgumentException;

final class Password extends LaravelPassword
{
    public function __construct()
    {
        $this->min = 1;
    }

    /**
     * Adds validation rule
     *
     * @param callable $rule
     * @return $this
     */
    public function addRule(callable $rule)
    {
        array_push($this->customRules, $rule);

        return $this;
    }

    /**
     * Specify additional validation rules that should be merged with the default rules during validation.
     *
     * @param  Closure|string|array  $rules
     * @return $this
     */
    public function rules($rules)
    {
        $wrapped = Arr::wrap($rules);

        if (is_callable($wrapped)) {
            $this->addRule($wrapped);
        } elseif (Arr::accessible($wrapped)) {
            foreach ((array) $wrapped as $value) {
                $this->addRule($value);
            }
        }

        return $this;
    }

    /**
     * DO NOT USE!!
     *
     * Use the {@see Password::setMin()} method instead.
     * @throws InvalidArgumentException
     */
    public static function min($size)
    {
        throw new InvalidArgumentException('This function is not supported.');
    }

    /**
     * Set the minimum size of the password.
     * The existing min() function creates a new Password instance with the default values, so this changes the existing instance.
     *
     * @param  int  $size
     * @return $this
     */
    public function setMin(int $size)
    {
        $this->min = $size;

        return $this;
    }

    /**
     * Sets the maximum length of the password.
     *
     * @param int $size
     * @return $this
     */
    public function setMax(int $size)
    {
        return $this->max($size);
    }
}