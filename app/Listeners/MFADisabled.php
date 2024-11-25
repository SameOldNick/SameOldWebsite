<?php

namespace App\Listeners;

use App\Components\MFA\Events\OTP\BackupCodeVerified;
use App\Notifications\BackupCodeUsed;
use Illuminate\Support\Facades\Notification;

class MFADisabled
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
    public function handle(BackupCodeVerified $event): void
    {
        Notification::send($event->authenticatable, new BackupCodeUsed);
    }
}
