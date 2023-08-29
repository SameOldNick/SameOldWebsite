<?php

namespace App\Components\Menus\Contracts;

interface Resolver
{
    /**
     * Resolves the link
     *
     * @return string URL
     */
    public function resolve();
}
