<?php

namespace App\Components\Menus\Facades;

use Illuminate\Support\Facades\Facade;

class Menus extends Facade
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
        return 'menus';
    }
}
