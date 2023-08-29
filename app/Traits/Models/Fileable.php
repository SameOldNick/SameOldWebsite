<?php

namespace App\Traits\Models;

use App\Models\File;

trait Fileable
{
    /**
     * Gets the File for this Model.
     *
     * @return mixed
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
