<?php

namespace App\Listeners\Backup;

use App\Models\Backup;
use Spatie\Backup\Events\BackupHasFailed;

class FailedBackup
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
    public function handle(BackupHasFailed $event): void
    {
        Backup::create([
            'error_message' => $event->exception->getMessage(),
        ]);
    }
}
