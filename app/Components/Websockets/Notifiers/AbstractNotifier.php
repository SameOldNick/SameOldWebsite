<?php

namespace App\Components\Websockets\Notifiers;

use App\Components\Websockets\Notifications\BroadcastNotification;
use Illuminate\Support\Facades\Notification;

abstract class AbstractNotifier
{
    /**
     * Sends notification to notifiable
     *
     * @param  mixed  $notifiable
     * @return void
     */
    public function notify($notifiable, BroadcastNotification $notification)
    {
        Notification::send($notifiable, $notification);
    }
}
