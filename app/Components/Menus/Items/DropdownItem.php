<?php

namespace App\Components\Menus\Items;

use App\Components\Menus\Links\Matchers\DropdownMatcher;
use App\Components\Menus\Menu;
use App\Components\Menus\Traits\HasMatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Traits\ForwardsCalls;

class DropdownItem extends Item
{
    use ForwardsCalls;
    use HasMatcher;

    protected $text;

    protected $props;

    protected $menu;

    public function __construct(Menu $parent, string $text, array $props = [])
    {
        parent::__construct($parent);

        $this->text = $text;
        $this->props = collect($props);
        $this->menu = new Menu($parent);

        $this->setMatcher(new DropdownMatcher($this));
    }

    /**
     * Gets the dropdown text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * {@inheritDoc}
     */
    public function isActive(Request $request)
    {
        $matcher = $this->getMatcher();

        return ! is_null($matcher) && $matcher->matches($request);
    }

    /**
     * Forwards method calls to Menu object
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return $this This DropdownItem instance
     */
    public function __call($name, $arguments)
    {
        return $this->forwardDecoratedCallTo($this->menu, $name, $arguments);
    }
}
