<?php

namespace App\Components\Menus\Render;

use App\Components\Menus\Contracts\MultiLevelRenderer;
use App\Components\Menus\Contracts\SingleLevelRenderer;
use App\Components\Menus\Items\DropdownItem;
use App\Components\Menus\Items\LinkItem;
use App\Components\Menus\Items\MenuDivider;
use App\Components\Menus\Items\RenderableItem;
use App\Components\Menus\Menu;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;

class Adapter
{
    /**
     * Renderer to use
     *
     * @var SingleLevelRenderer
     */
    protected $renderer;

    /**
     * Initializes adapter
     */
    public function __construct(SingleLevelRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Renders menu
     *
     * @return string HTML
     */
    public function render(Menu $menu)
    {
        $views = $this->renderItems($menu->items()->all(), 1);

        $outer = $this->renderer->renderOuter($menu, $this->renderViews($views));

        return $this->renderView($outer);
    }

    /**
     * Renders items recursively
     *
     * @return array Array of views
     */
    protected function renderItems(array $items, int $depth)
    {
        $views = collect();

        foreach ($items as $item) {
            $view = null;

            if ($item instanceof LinkItem) {
                $view = $this->renderView($this->renderer->renderItem($item, $depth));
            } elseif ($item instanceof MenuDivider) {
                $view = $this->renderView($this->renderer->renderDivider($item, $depth));
            } elseif ($item instanceof DropdownItem && $this->renderer instanceof MultiLevelRenderer) {
                $innerViews = $this->renderItems($item->items()->all(), $depth + 1);

                $view = $this->renderer->renderDropdown($item, $this->renderViews($innerViews), $depth);
            } elseif ($item instanceof RenderableItem) {
                $renderable = $item->getRenderable();

                // If renderer has method to render renderable, send it to that first.
                // Otherwise, send it directly to the renderView method.

                $view = $this->renderView(method_exists($this->renderer, 'renderRenderable') ? $this->renderer->renderRenderable($renderable, $depth) : $renderable);
            }

            if (! is_null($view)) {
                $views->push($view);
            }
        }

        return $views->all();
    }

    /**
     * Renders array of views into single HTML string
     *
     * @return string
     */
    protected function renderViews(array $views)
    {
        return Arr::join(Arr::map($views, fn ($view) => $this->renderView($view)), PHP_EOL);
    }

    /**
     * Renders a view
     *
     * @param  mixed  $view
     * @return string
     */
    protected function renderView($view)
    {
        if ($view instanceof View) {
            return $view->render();
        } elseif ($view instanceof Htmlable) {
            return $view->toHtml();
        } else {
            // Otherwise return $view as string
            return (string) $view;
        }
    }
}
