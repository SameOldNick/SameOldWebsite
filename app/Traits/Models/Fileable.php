<?php

namespace App\Traits\Models;

use App\Models\File;

/**
 * @property-read File|null $file
 */
trait Fileable
{
    /**
     * Gets the File for this Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function file()
    {
        return $this->morphOne(File::class, 'fileable');
    }

    /**
     * Checks if file should be removed when model is deleted.
     *
     * @return bool
     */
    public function removeFileOnDelete()
    {
        return true;
    }
}
