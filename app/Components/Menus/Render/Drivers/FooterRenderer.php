<?php

namespace App\Components\Menus\Render\Drivers;

use App\Components\Menus\Contracts\MultiLevelRenderer;
use App\Components\Menus\Items\DropdownItem;
use App\Components\Menus\Items\LinkItem;
use App\Components\Menus\Items\MenuDivider;
use App\Components\Menus\Menu;
use App\Components\Menus\Traits\HasAttributes;
use Illuminate\Http\Request;

class FooterRenderer implements MultiLevelRenderer
{
    use HasAttributes;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function renderOuter(Menu $menu, string $inner)
    {
        return view('components.menus.footer.outer', ['attributes' => $this->getAttributes()->merge($menu->getProps()->all()), 'inner' => $inner]);
    }

    public function renderItem(LinkItem $item, int $depth)
    {
        $data = ['item' => $item, 'active' => $item->getMatcher()->matches($this->request)];

        return view($depth == 1 ? 'components.menus.footer.item' : 'components.menus.footer.dropdown-item', $data);
    }

    public function renderDropdown(DropdownItem $dropdown, string $inner, int $depth)
    {
        return view('components.menus.footer.dropdown', compact('dropdown', 'inner'));
    }

    public function renderDivider(MenuDivider $divider, int $depth)
    {
        return view($depth == 1 ? 'components.menus.footer.divider' : 'components.menus.footer.dropdown-divider');
    }
}
