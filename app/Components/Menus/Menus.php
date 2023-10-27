<?php

namespace App\Components\Menus;

use OutOfBoundsException;

class Menus
{
    protected $menus;

    public function __construct()
    {
        $this->menus = collect();
    }

    /**
     * Creates new menu
     *
     * @param string $name
     * @param callable $callback Called with Menu instance
     * @return Menu
     */
    public function create($name, callable $callback)
    {
        $this->menus[$name] = new Menu();

        $callback($this->menus[$name]);

        return $this->menus[$name];
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
        if (! $this->menus->has($name)) {
            throw new OutOfBoundsException("Menu '{$name}' does not exist.");
        }

        return $this->menus[$name];
    }
}
