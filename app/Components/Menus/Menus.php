<?php

namespace App\Components\Menus;

use OutOfBoundsException;

class Menus
{
    protected $callbacks;

    protected $menus;

    public function __construct()
    {
        $this->menus = collect();
        $this->callbacks = [];
    }

    /**
     * Creates new menu
     *
     * @param string $name
     * @param callable $callback Called with Menu instance
     * @return Menu
     */
    public function create(string $name, callable $callback)
    {
        $this->menus[$name] = new Menu();

        $callback($this->menus[$name]);

        return $this->menus[$name];
    }

    /**
     * Lazily creates a menu.
     *
     * @param string $name
     * @param callable $callback Called when menu is first accessed.
     * @return $this
     */
    public function lazyCreate(string $name, callable $callback)
    {
        $this->callbacks[$name] = $callback;

        return $this;
    }

    /**
     * Gets menu with name
     *
     * @param string $name
     * @return Menu
     * @throws OutOfBoundsException Thrown if menu doesn't exist.
     */
    public function get($name)
    {
        if (isset($this->callbacks[$name])) {
            $this->create($name, $this->callbacks[$name]);
        }

        if (! $this->menus->has($name)) {
            throw new OutOfBoundsException("Menu '{$name}' does not exist.");
        }

        return $this->menus[$name];
    }
}
