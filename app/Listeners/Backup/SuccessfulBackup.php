<?php

namespace App\Listeners\Backup;

use App\Models\Backup;
use Illuminate\Support\Facades\Auth;
use Spatie\Backup\Events\BackupWasSuccessful;

class SuccessfulBackup
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
    public function handle(BackupWasSuccessful $event): void
    {
        Backup::create()->file()->save(Backup::createFile(
            $event->backupDestination->newestBackup(),
            $event->backupDestination,
            Auth::user()
        ));
    }
}
