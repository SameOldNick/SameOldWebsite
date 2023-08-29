<?php

namespace App\Components\Menus\Links\Matchers;

use App\Components\Menus\Contracts\Matcher;
use Illuminate\Http\Request;

class CallbackMatcher implements Matcher
{
    protected $callback;

    /**
     * Constructs CallbackMatcher
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritDoc
     */
    public function matches(Request $request)
    {
        return (bool) call_user_func($this->callback, $request);
    }
}
