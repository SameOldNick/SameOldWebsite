<?php

namespace App\Components\Ntfy\Channels;

use App\Components\Ntfy\Contracts\NtfyNotification;
use App\Components\Ntfy\Services\Ntfy;
use Illuminate\Notifications\Notification;
use Ntfy\Message;

class NtfyChannel
{
    public function __construct(protected readonly Ntfy $ntfy)
    {
        //
    }

    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        $message = $this->toMessage($notifiable, $notification);

        $this->ntfy->send($message);
    }

    /**
     * Check if ntfy is enabled and configured.
     */
    public function isEnabled(): bool
    {
        return config('services.ntfy.enabled', false);
    }

    /**
     * Get the ntfy message for the given notifiable.
     */
    protected function toMessage(object $notifiable, Notification $notification): Message
    {
        // Checks if the notification implements the NtfyNotification contract or has a toNtfy method
        // The latter is for consistency with other notification channels in Laravel
        if ($notification instanceof NtfyNotification || method_exists($notification, 'toNtfy')) {

            return $notification->toNtfy($notifiable);
        }

        throw new \InvalidArgumentException('Notification is not an instance of NtfyNotification.');
    }
}
