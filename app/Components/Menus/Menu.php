<?php

namespace App\Components\Menus;

use App\Components\Menus\Contracts\Matcher;
use App\Components\Menus\Contracts\Resolver;
use App\Components\Menus\Items\DropdownItem;
use App\Components\Menus\Items\Item;
use App\Components\Menus\Items\LinkItem;
use App\Components\Menus\Items\MenuDivider;
use App\Components\Menus\Items\RenderableItem;
use App\Components\Menus\Links\Matchers;
use App\Components\Menus\Links\Resolvers;
use App\Components\Menus\Traits\HasProps;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\View\ComponentAttributeBag;

class Menu
{
    use Conditionable;
    use HasProps;

    protected $parent;

    protected $items = [];

    public function __construct(?self $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Adds a route menu item
     *
     * @param  string|array  $name  Can be name of route or array of parameters to pass to route() function
     * @param  string  $text  Text content for menu item
     * @param  callable|null  $callback  If callable, called with LinkItem after it's created
     * @return $this
     */
    public function route($name, $text, ?callable $callback = null)
    {
        $params = Arr::wrap($name);

        $resolver = new Resolvers\RouteNameResolver($params);
        $matcher = new Matchers\RouteNameMatcher($params[0], isset($params[1]) && is_array($params[1]) ? $params[1] : []);

        return $this->item($resolver, $matcher, $text, $callback);
    }

    /**
     * Adds a url menu item
     *
     * @param  string|array  $url  Can be url or array of parameters to pass to url() function
     * @param  string  $text  Text content for menu item
     * @param  callable|null  $callback  If callable, called with LinkItem after it's created
     * @return $this
     */
    public function url($url, $text, ?callable $callback = null)
    {
        $params = Arr::wrap($url);

        $resolver = new Resolvers\UrlResolver($params);
        $matcher = new Matchers\PatternMatcher($params[0]);

        return $this->item($resolver, $matcher, $text, $callback);
    }

    /**
     * Adds a action menu item
     *
     * @param  string|array  $name  Can be action as string or array of parameters to pass to action() function
     * @param  string  $text  Text content for menu item
     * @param  callable|null  $callback  If callable, called with LinkItem after it's created
     * @return $this
     */
    public function action($name, $text, ?callable $callback = null)
    {
        // Sets params to 2D array if action callable array is specified
        $params = $this->isActionCallableArray($name) ? [$name] : Arr::wrap($name);

        $resolver = new Resolvers\ActionResolver($params);
        $matcher = new Matchers\ActionMatcher($params[0]);

        return $this->item($resolver, $matcher, $text, $callback);
    }

    /**
     * Adds item to menu
     *
     * @param  Resolver  $resolver  Resolver to use for URL
     * @param  Matcher  $matcher  Matcher to check
     * @param  string  $text  Text content for menu item
     * @param  callable|null  $callback  If callable, called with LinkItem after it's created
     * @return $this
     */
    public function item(Resolver $resolver, Matcher $matcher, $text, ?callable $callback = null)
    {
        $item = new LinkItem($this, $resolver, $matcher, $text);

        if (is_callable($callback)) {
            call_user_func($callback, $item);
        }

        return $this->addItem($item);
    }

    /**
     * Adds renderable item
     *
     * @param  mixed  $renderable  View, Htmlable, or string
     * @return $this
     */
    public function renderable($renderable, array $props = [])
    {
        return $this->addItem(new RenderableItem($this, $renderable, $props));
    }

    /**
     * Adds menu divider
     *
     * @return $this
     */
    public function divider(array $props = [])
    {
        return $this->addItem(new MenuDivider($this, $props));
    }

    /**
     * Adds dropdown menu item
     *
     * @param  string  $text  Text for dropdown
     * @param  callable  $callback  Called with DropdownItem instance
     * @return $this
     */
    public function dropdown($text, ?callable $callback = null)
    {
        $dropdown = new DropdownItem($this, $text);

        if (is_callable($callback)) {
            call_user_func($callback, $dropdown);
        }

        return $this->addItem($dropdown);
    }

    /**
     * Adds item
     *
     * @return $this
     */
    protected function addItem(Item $item)
    {
        array_push($this->items, $item);

        return $this;
    }

    /**
     * Gets the items in this menu
     *
     * @return \Illuminate\Support\Collection
     */
    public function items()
    {
        return collect($this->items);
    }

    /**
     * Renders the menu with the specified renderer
     *
     * @param  string|null  $renderer  Name of renderer driver to use
     * @return string
     */
    public function render($renderer)
    {
        return app('menus.render')->driver($renderer)->render($this);
    }

    /**
     * Renders the menu with the specified renderer and attributes
     *
     * @param  string|null  $renderer  Name of renderer driver to use
     * @param  ComponentAttributeBag $attributes Attributes specified in view.
     * @return string
     */
    public function renderWithAttributes($renderer, ComponentAttributeBag $attributes)
    {
        return app('menus.render')->driver($renderer)->withAttributes($attributes)->render($this);
    }

    /**
     * Checks if parameters is an action callable array
     *
     * @param  mixed  $params
     * @return bool
     */
    protected function isActionCallableArray($params)
    {
        return is_array($params) && count($params) == 2 && is_string($params[0]) && is_string($params[1]);
    }

    /**
     * Sets property with method name
     *
     * @param  string  $name  Name of property
     * @param  array  $arguments  First argument is used as value for property
     * @return $this
     */
    public function __call($name, $arguments)
    {
        return $this->setProp($name, $arguments[0]);
    }
}
