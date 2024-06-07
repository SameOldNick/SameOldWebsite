<?php

namespace App\Models\Collections;

use App\Models\Backup;
use Illuminate\Database\Eloquent\Collection;

/**
 * @mixin Collection<int, Backup>
 */
class BackupCollection extends Collection
{
    /**
     * Gets backups with status
     *
     * @param string $status
     * @return Collection<int, Backup>
     */
    public function status(string $status)
    {
        return $this->filter(fn (Backup $backup) => $backup->status === $status);
    }
}
