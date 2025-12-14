<?php

namespace App\Components\Ntfy\Facades;

use App\Components\Ntfy\Services\Ntfy as NtfyService;
use Illuminate\Support\Facades\Facade;

class Ntfy extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return NtfyService::class;
    }
}
