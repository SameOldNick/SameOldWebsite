<?php

namespace App\Components\Menus\Traits;

use Illuminate\View\ComponentAttributeBag;

trait HasProps
{
    protected $props;

    /**
     * Sets property
     *
     * @param  string  $name  Property name
     * @param  mixed  $arguments  Arguments for property
     * @return $this
     */
    public function setProp($name, $arguments)
    {
        $this->props[$name] = $arguments;

        return $this;
    }

    /**
     * Gets all properties
     *
     * @return \Illuminate\Support\Collection
     */
    public function getProps()
    {
        return collect($this->props);
    }

    /**
     * Checks if property with key exists
     *
     * @param  string  $key
     * @return bool
     */
    public function hasProp($key)
    {
        return isset($this->props[$key]);
    }

    /**
     * Gets property
     *
     * @param  string  $name  Property name
     * @param  mixed  $default  What to return if property doesn't exist (default: null)
     * @return mixed
     */
    public function getProp($name, $default = null)
    {
        return $this->props[$name] ?? $default;
    }

    /**
     * Gets properties as an ComponentAttributeBag
     *
     * @param  string  $key  Key of array to get as ComponentAttributeBag. If null, all props are included. (default: null)
     * @return ComponentAttributeBag
     */
    public function attributes(?string $key = null)
    {
        return new ComponentAttributeBag($this->getProp($key, $this->getProps()->all()));
    }
}
