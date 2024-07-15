<?php

namespace App\Components\Macros;

use Illuminate\Support\Stringable;

class StringableMixin
{
    public function hash()
    {
        return function ($algo, array $options = []) {
            /**
             * @var Stringable $this
             */
            $hash = hash($algo, $this->toString(), options: $options);

            return new Stringable($hash);
        };
    }

    public function safeExactly()
    {
        return function ($value) {
            /**
             * @var Stringable $this
             */

            return hash_equals($this->toString(), (string) $value);
        };
    }
}
