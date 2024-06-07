<?php

namespace App\Listeners\Backup;

use App\Models\Backup;
use Spatie\Backup\BackupDestination\Backup as SpatieBackup;
use Spatie\Backup\Events\CleanupWasSuccessful;

class SuccessfulCleanup
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CleanupWasSuccessful $event): void
    {
        $backupDestination = $event->backupDestination;
        $existing = $backupDestination->backups();

        foreach (Backup::all() as $model) {
            $found = $existing->first(function (SpatieBackup $backup) use ($model, $backupDestination) {
                if (is_null($model->file) || $backupDestination->diskName() !== $model->file->disk) {
                    return false;
                }

                return $model->file->path === $backup->path();
            });

            // Test if exists() is false, as it will test the 'exists' property which is set to false when delete is called.
            if (! is_null($found) && ! $found->exists()) {
                $model->delete();
            }
        }
    }
}
