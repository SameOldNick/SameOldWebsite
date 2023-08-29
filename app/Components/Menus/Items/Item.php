<?php

namespace App\Components\Menus\Items;

use App\Components\Menus\Menu;
use Illuminate\Http\Request;

abstract class Item
{
    protected $parent;

    /**
     * Initializes item
     *
     * @param Menu $parent
     */
    public function __construct(Menu $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Gets the parent menu
     *
     * @return Menu
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Checks if menu item is active
     *
     * @param Request $request
     * @return bool
     */
    public function isActive(Request $request)
    {
        return false;
    }
}
