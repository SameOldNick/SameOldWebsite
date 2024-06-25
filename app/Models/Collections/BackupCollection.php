<?php

namespace App\Models\Collections;

use App\Models\Backup;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends Collection<int, Backup>
 */
class BackupCollection extends Collection
{
    /**
     * Gets backups with status
     *
     * @return static
     */
    public function status(string $status)
    {
        return $this->filter(fn (Backup $backup) => $backup->status === $status);
    }
}
