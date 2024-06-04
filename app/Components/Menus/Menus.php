<?php

namespace App\Components\Menus;

use OutOfBoundsException;

class Menus
{
    protected $callbacks;

    protected $afterCreated;

    protected $menus;

    public function __construct()
    {
        $this->menus = collect();
        $this->callbacks = [];
        $this->afterCreated = [];
    }

    /**
     * Creates new menu
     *
     * @param  callable  $callback  Called with Menu instance
     * @return Menu
     */
    public function create(string $name, callable $callback)
    {
        $this->menus[$name] = new Menu();

        $callback($this->menus[$name]);

        if (isset($this->afterCreated[$name])) {
            foreach ($this->afterCreated[$name] as $afterCreatedCallback) {
                $afterCreatedCallback($this->menus[$name]);
            }
        }

        return $this->menus[$name];
    }

    /**
     * Adds menu that is created lazily.
     *
     * @param  callable  $callback  Called when menu is first accessed.
     * @return $this
     */
    public function add(string $name, callable $callback)
    {
        $this->callbacks[$name] = $callback;

        return $this;
    }

    /**
     * Adds callback for after menu is created.
     *
     * @return $this
     */
    public function afterCreated(string $name, callable $callback)
    {
        if (! isset($this->afterCreated[$name])) {
            $this->afterCreated[$name] = [];
        }

        array_push($this->afterCreated[$name], $callback);

        return $this;
    }

    /**
     * Gets menu with name
     *
     * @param  string  $name
     * @return Menu
     *
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
