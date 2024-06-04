<?php

namespace App\Components\Passwords\Rules;

use App\Components\Passwords\Contracts\Rule;
use App\Components\Passwords\Password;
use Closure;
use Illuminate\Support\Facades\App;

class CustomRule implements Rule
{
    public function __construct(
        protected readonly Closure $callback
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Password $password): Password
    {
        return App::call($this->callback, compact('password'));
    }
}
