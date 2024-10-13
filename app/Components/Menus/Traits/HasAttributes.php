<?php

namespace App\Components\Menus\Traits;

use Illuminate\View\ComponentAttributeBag;

trait HasAttributes
{
    /**
     * Attribute bag
     */
    protected ComponentAttributeBag $attributes;

    /**
     * Sets attributes
     *
     * @return $this
     */
    public function withAttributes(ComponentAttributeBag $attributes)
    {
        $this->attributes = $this->createAttributesBag($attributes->all());

        return $this;
    }

    /**
     * Gets the attributes
     *
     * @return ComponentAttributeBag
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Creates ComponentAttributeBag instance
     *
     * @return ComponentAttributeBag
     */
    protected function createAttributesBag(array $attributes)
    {
        return new ComponentAttributeBag($attributes);
    }
}
