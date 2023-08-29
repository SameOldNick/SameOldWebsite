<?php

namespace App\Components\Menus\Links\Matchers;

use App\Components\Menus\Contracts\Matcher;
use Illuminate\Http\Request;

class RouteNameMatcher implements Matcher
{
    protected $name;
    protected $params;

    public function __construct(string $name, array $params = [])
    {
        $this->name = $name;
        $this->params = $params;
    }

    /**
     * @inheritDoc
     */
    public function matches(Request $request)
    {
        $route = $request->route();

        if (is_null($route) || !$route->named($this->name))
            return false;

         return count($this->params) > 0 ? empty(array_diff_assoc($this->params, $route->parameters())) : true;
    }
}
