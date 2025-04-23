<?php

namespace App\Listeners;

use App\Notifications\PasswordChanged as PasswordChangedNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Notification;

class PasswordChanged
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
    public function handle(PasswordReset $event): void
    {
        Notification::send($event->user, new PasswordChangedNotification);
    }
}
