<?php

namespace App\Components\MFA\Facades;

use Illuminate\Support\Facades\Facade;

class MFA extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mfa.auth';
    }
}
