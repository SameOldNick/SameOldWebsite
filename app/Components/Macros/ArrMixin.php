<?php

namespace App\Components\Macros;

use App\Components\Macros\Helpers\ArrayCodeGenerator;
use Illuminate\Support\Arr;

class ArrMixin
{
    /**
     * Checks if key in array is an index (number greater than or equal to zero)
     *
     * @return callable
     */
    public function isKeyIndex()
    {
        return function ($array, $key) {
            $keys = array_keys($array);

            return isset($keys[$key]) && $keys[$key] === $key;
        };
    }

    /**
     * Checks if key in array is associative (not an index)
     *
     * @return callable
     */
    public function isKeyAssoc()
    {
        return function ($array, $key) {
            return ! Arr::isKeyIndex($array, $key);
        };
    }

    /**
     * Checks if keys are filled in array (using built-in filled function in Laravel).
     *
     * @return callable
     */
    public function filled()
    {
        return function ($array, $keys) {
            if (empty($array)) {
                return empty($keys);
            }

            $found = Arr::only($array, $keys);
            $filtered = array_filter($found, fn ($value) => filled($value));

            return count($filtered) === count($keys);
        };
    }

    /**
     * Exports array as PHP code.
     *
     * @return callable
     */
    public function export() {
        return function ($array, $ignoreIndexes = true, $shortSyntax = true, $indent = '    ') {
            /**
             * Macros don't play nice when it comes to recursion.
             * We'll send it off to another class to allow recursion.
             */

            return ArrayCodeGenerator::generate($array, 1, $ignoreIndexes, $shortSyntax, $indent);
        };
    }
}
