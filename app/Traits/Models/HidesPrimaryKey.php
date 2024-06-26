<?php

namespace App\Traits\Models;

/**
 * Hides the primary key from serialization.
 */
trait HidesPrimaryKey
{
    protected function initializeHidesPrimaryKey()
    {
        $this->makeHidden($this->getKeyName());
    }
}
