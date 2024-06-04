<?php

namespace App\Components\Menus\Contracts;

use App\Components\Menus\Items\DropdownItem;

interface MultiLevelRenderer extends SingleLevelRenderer
{
    /**
     * Renders a dropdown
     *
     * @param  DropdownItem  $dropdown  Dropdown element
     * @param  string  $inner  Rendered inner dropdown items
     * @param  int  $depth  Depth of dropdown
     * @return string
     */
    public function renderDropdown(DropdownItem $dropdown, string $inner, int $depth);
}
