<?php

namespace App\Components\SweetAlert;

use Illuminate\Support\Facades\Facade;

class Swal extends Facade
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
        return SweetAlerts::class;
    }
}
