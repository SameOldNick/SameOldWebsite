<?php

namespace App\Components\Menus\Traits;

use App\Components\Menus\Contracts\Matcher;

trait HasMatcher
{
    protected $matcher;

    /**
     * Gets the link matcher for this item
     *
     * @return Matcher|null
     */
    public function getMatcher()
    {
        return $this->matcher;
    }

    /**
     * Sets the link matcher to use for this item
     *
     * @return $this
     */
    public function setMatcher(Matcher $matcher)
    {
        $this->matcher = $matcher;

        return $this;
    }
}
