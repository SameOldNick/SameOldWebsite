<?php

namespace App\Components\Menus\Items;

use App\Components\Menus\Contracts\Matcher;
use App\Components\Menus\Contracts\Resolver;
use App\Components\Menus\Menu;
use App\Components\Menus\Traits\HasMatcher;
use App\Components\Menus\Traits\HasProps;
use Illuminate\Http\Request;

class LinkItem extends Item
{
    use HasMatcher;
    use HasProps;

    protected $resolver;

    protected $content;

    public function __construct(Menu $parent, Resolver $resolver, Matcher $matcher, $content, array $props = [])
    {
        parent::__construct($parent);

        $this->resolver = $resolver;
        $this->matcher = $matcher;
        $this->content = $content;
        $this->props = collect($props);
    }

    /**
     * Gets the link resolver for this item
     *
     * @return Resolver
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * Gets menu item content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function isActive(Request $request)
    {
        return $this->getMatcher()->matches($request);
    }

    public function __call($name, $arguments)
    {
        return $this->setProp($name, $arguments[0]);
    }
}
