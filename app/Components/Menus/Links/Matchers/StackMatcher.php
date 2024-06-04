<?php

namespace App\Components\Menus\Links\Matchers;

use App\Components\Menus\Contracts\Matcher;
use Illuminate\Http\Request;

class StackMatcher implements Matcher
{
    protected $stack;

    /**
     * Constructs StackMatcher
     *
     * @param  array<int, Matcher>  $stack
     */
    public function __construct(array $stack)
    {
        $this->stack = $stack;
    }

    /**
     * {@inheritDoc}
     */
    public function matches(Request $request)
    {
        foreach ($this->stack as $matcher) {
            if ($matcher->matches($request)) {
                return true;
            }
        }

        return false;
    }
}
