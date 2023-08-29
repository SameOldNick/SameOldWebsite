<?php

namespace App\Models\Concerns;

/**
 * Hides the primary key from serialization.
 */
trait HidesPrimaryKey {

    protected function initializeHidesPrimaryKey() {
        $this->makeHidden($this->getKeyName());
    }
}
