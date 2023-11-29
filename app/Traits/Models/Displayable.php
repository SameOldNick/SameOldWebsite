<?php

namespace App\Traits\Models;

use Illuminate\Support\Str;

trait Displayable
{
    /**
     * Gets the ID to use for a HTML element.
     *
     * @return string
     */
    public function generateElementId() {
        $prefix = class_basename(static::class);

        return sprintf('%s-%s', Str::kebab($prefix), $this->getElementIdSuffix());
    }

    /**
     * Gets the suffix for the element ID.
     *
     * @return string
     */
    protected function getElementIdSuffix(): string {
        return $this->getKey();
    }
}
