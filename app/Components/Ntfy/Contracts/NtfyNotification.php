<?php

namespace App\Components\Ntfy\Contracts;

use Ntfy\Message;

interface NtfyNotification
{
    /**
     * Convert the notification to an ntfy Message.
     */
    public function toNtfy(object $notifiable): Message;
}
