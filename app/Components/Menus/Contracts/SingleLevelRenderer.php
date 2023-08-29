<?php

namespace App\Components\Menus\Contracts;

use App\Components\Menus\Items\DropdownItem;
use App\Components\Menus\Items\LinkItem;
use App\Components\Menus\Items\MenuDivider;
use App\Components\Menus\Menu;

interface SingleLevelRenderer
{
    /**
     * Renders the container for the entire menu
     *
     * @param Menu $menu
     * @param string $inner HTML with inner elements
     * @return string
     */
    public function renderOuter(Menu $menu, string $inner);

    /**
     * Renders a single item
     *
     * @param LinkItem $item Item to render
     * @param int $depth Depth of item (1 means first/main level)
     * @return string
     */
    public function renderItem(LinkItem $item, int $depth);

    /**
     * Renders a divider
     *
     * @param MenuDivider $divider Divider to render
     * @param int $depth Depth of divider (1 means first/main level)
     * @return string
     */
    public function renderDivider(MenuDivider $divider, int $depth);
}
