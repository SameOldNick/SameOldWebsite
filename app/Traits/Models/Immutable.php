<?php

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * Sets a model as immutable
 */
trait Immutable
{
    /**
     * Is the model immutable
     *
     * @var boolean
     */
    private $immutable = false;

    /**
     * Checks if model is immutable.
     *
     * @return boolean
     */
    public function isImmutable(): bool {
        return $this->immutable;
    }

    /**
     * Sets model as immutable.
     * Note: Once a model is immutable, it cannot be changed back to mutable.
     *
     * @param boolean $enabled
     * @return static
     * @throws RuntimeException Thrown if trying to change model from immutable to mutable.
     */
    public function setImmutable(bool $enabled = true): static {
        if ($this->isImmutable() && !$enabled)
            throw new RuntimeException("An immutable model cannot be changed to mutable.");

        $this->immutable = $enabled;

        return $this;
    }

    /**
     * Boots the trait
     *
     * @return void
     */
    public static function bootImmutable()
    {
        static::updating(function(Model $model) {
            if ($model->isImmutable())
                throw new RuntimeException("This model is immutable and cannot be updated.");
        });

        static::deleting(function(Model $model) {
            if ($model->isImmutable())
                throw new RuntimeException("This model is immutable and cannot be deleted.");
        });
    }
}
