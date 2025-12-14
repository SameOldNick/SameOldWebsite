<?php

namespace App\Components\Ntfy\Facades;

use App\Components\Ntfy\Services\Ntfy as NtfyService;
use App\Components\Ntfy\Services\NtfyFake;
use Illuminate\Support\Facades\Facade;

class Ntfy extends Facade
{
    /**
     * Replace the bound instance with a fake.
     */
    public static function fake(): NtfyFake
    {
        static::swap($fake = new NtfyFake);

        return $fake;
    }

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return NtfyService::class;
    }
}
