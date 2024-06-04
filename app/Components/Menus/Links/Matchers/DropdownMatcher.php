<?php

namespace App\Components\Menus\Links\Matchers;

use App\Components\Menus\Contracts\Matcher;
use App\Components\Menus\Items\DropdownItem;
use App\Components\Menus\Items\LinkItem;
use Illuminate\Http\Request;

class DropdownMatcher implements Matcher
{
    protected $dropdownItem;

    /**
     * Initializes DropdownMatcher
     */
    public function __construct(DropdownItem $dropdownItem)
    {
        $this->dropdownItem = $dropdownItem;
    }

    /**
     * {@inheritDoc}
     */
    public function matches(Request $request)
    {
        foreach ($this->dropdownItem->items() as $item) {
            if ($item instanceof LinkItem && $item->isActive($request)) {
                return true;
            }
        }

        return false;
    }
}
