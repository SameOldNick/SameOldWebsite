<?php

namespace App\Components\Menus\Links\Matchers;

use App\Components\Menus\Contracts\Matcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class ActionMatcher implements Matcher
{
    protected $action;

    public function __construct($action)
    {
        $this->action = $action;
    }

    /**
     * @inheritDoc
     */
    public function matches(Request $request)
    {
        $route = Route::getRoutes()->getByAction($this->parseAction($this->action));

        return ! is_null($route) && $route->matches($request);
    }

    /**
     * Transforms action into proper string
     *
     * @param mixed $action
     * @return string
     */
    protected function parseAction($action)
    {
        // Taken from \Illuminate\Routing\UrlGenerator::action()
        if (is_array($action)) {
            $action = '\\'.implode('@', $action);
        }

        if (URL::getRootControllerNamespace() && ! str_starts_with($action, '\\')) {
            return URL::getRootControllerNamespace().'\\'.$action;
        } else {
            return trim($action, '\\');
        }
    }
}
