<?php

namespace App\Components\Menus\Links\Resolvers;

use App\Components\Menus\Contracts\Resolver;

class StringResolver implements Resolver
{
    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function resolve()
    {
        return $this->url;
    }
}
