<?php

namespace App\Components\OAuth\Facades;

use Illuminate\Support\Facades\Facade;

class OAuth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'oauth';
    }
}
