<?php

namespace App\Components\Menus\Links\Matchers;

use App\Components\Menus\Contracts\Matcher;
use Illuminate\Http\Request;

class PatternMatcher implements Matcher
{
    protected $pattern;

    /**
     * Initializes PatternMatcher
     *
     * @param string $pattern Pattern to pass to Request::is method
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @inheritDoc
     */
    public function matches(Request $request)
    {
        return $request->is($this->pattern);
    }
}
