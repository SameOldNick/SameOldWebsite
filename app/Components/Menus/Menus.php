<?php

namespace App\Components\Menus;

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
     */
    public function get($name)
    {
        return $this->menus[$name];
    }
}
