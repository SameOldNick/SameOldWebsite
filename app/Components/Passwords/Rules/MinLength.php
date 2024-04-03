<?php

namespace App\Components\Passwords\Rules;

use App\Components\Passwords\Contracts\Rule;
use App\Components\Passwords\Password;

class MinLength implements Rule {
    public function __construct(
        public readonly int $min
    )
    {

    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return $this->min > 0;
    }

    /**
     * @inheritDoc
     */
    public function configure(Password $password): Password {
        return $password->setMin($this->min);
    }
}
