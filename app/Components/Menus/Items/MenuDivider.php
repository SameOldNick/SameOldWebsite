<?php

namespace App\Components\Menus\Items;

use App\Components\Menus\Menu;
use App\Components\Menus\Traits\HasProps;

class MenuDivider extends Item
{
    use HasProps;

    public function __construct(Menu $parent, array $props = [])
    {
        parent::__construct($parent);

        $this->props = collect($props);
    }

    public function __call($name, $arguments)
    {
        return $this->setProp($name, $arguments);
    }
}
