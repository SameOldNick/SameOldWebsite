<?php

namespace App\Components\Menus\Render;

use Illuminate\Contracts\Container\Container;
use Illuminate\View\Component;

class MenuComponent extends Component
{
    protected $app;

    protected $name;

    protected $renderer;

    /**
     * Create a new component instance.
     *
     * @param Container $app Application container
     * @param string $name Name of menu to render
     * @param string $renderer Renderer to use. If null, default renderer (specified in manager) is used. (default: null)
     */
    public function __construct(Container $app, $name, $renderer = null)
    {
        $this->app = $app;
        $this->name = $name;
        $this->renderer = $renderer;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $menu = $this->app['menus']->get($this->name);

        return $menu->render($this->renderer);
    }
}
