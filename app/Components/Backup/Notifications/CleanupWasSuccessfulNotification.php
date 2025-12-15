<?php

namespace App\Components\Backup\Notifications;

use App\Components\Backup\Concerns\PullsNotificationConfiguration;
use App\Components\Ntfy\Services\MessageBuilder;
use Ntfy\Message as NtfyMessage;
use Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification as BaseNotification;

class CleanupWasSuccessfulNotification extends BaseNotification
{
    use PullsNotificationConfiguration;

    /**
     * Create an ntfy message representation of the notification.
     */
    public function toNtfy($notifiable): NtfyMessage
    {
        $message = [
            __('The cleanup process completed successfully.'),

            __('Backup Destination Properties:'),
            json_encode($this->backupDestinationProperties()->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ];

        return MessageBuilder::make()
            ->title(__('Cleanup Was Successful'))
            ->body(implode("\n", $message))
            ->build();
    }
}
