<?php

namespace App\Components\Menus\Contracts;

use Illuminate\Http\Request;

interface Matcher
{
    /**
     * Checks if request matches
     *
     * @return bool
     */
    public function matches(Request $request);
}
