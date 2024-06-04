<?php

namespace App\Components\Menus\Links\Resolvers;

use App\Components\Menus\Contracts\Resolver;

class ActionResolver implements Resolver
{
    protected $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve()
    {
        return call_user_func_array('action', $this->params);
    }
}
