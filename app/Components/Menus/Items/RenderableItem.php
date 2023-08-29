<?php

namespace App\Components\Menus\Items;

use App\Components\Menus\Menu;
use App\Components\Menus\Traits\HasProps;

class RenderableItem extends Item
{
    use HasProps;

    protected $renderable;

    public function __construct(Menu $parent, $renderable, array $props = [])
    {
        parent::__construct($parent);

        $this->renderable = $renderable;
        $this->props = collect($props);
    }

    /**
     * Gets the renderable item
     *
     * @return mixed
     */
    public function getRenderable()
    {
        return $this->renderable;
    }
}
