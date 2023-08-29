<?php

namespace App\Components\Menus\Contracts;

use Illuminate\Http\Request;

interface Matcher
{
    /**
     * Checks if request matches
     *
     * @param Request $request
     * @return bool
     */
    public function matches(Request $request);
}
