<?php

namespace App\Components\Menus\Render\Drivers;

use App\Components\Menus\Contracts\SingleLevelRenderer;
use App\Components\Menus\Items\LinkItem;
use App\Components\Menus\Items\MenuDivider;
use App\Components\Menus\Menu;
use App\Components\Menus\Traits\HasAttributes;
use Illuminate\Http\Request;

class ListGroupRenderer implements SingleLevelRenderer
{
    use HasAttributes;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function renderOuter(Menu $menu, string $inner)
    {
        return view('components.menus.listgroup.outer', ['attributes' => $this->getAttributes()->merge($menu->getProps()->all()), 'inner' => $inner]);
    }

    public function renderItem(LinkItem $item, int $depth)
    {
        $data = ['item' => $item, 'active' => $item->getMatcher()->matches($this->request)];

        return view('components.menus.listgroup.item', $data);
    }

    public function renderDivider(MenuDivider $divider, int $depth)
    {
        return view('components.menus.listgroup.divider');
    }
}
