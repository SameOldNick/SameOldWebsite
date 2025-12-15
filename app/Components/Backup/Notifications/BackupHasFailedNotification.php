<?php

namespace App\Components\Backup\Notifications;

use App\Components\Backup\Concerns\PullsNotificationConfiguration;
use App\Components\Ntfy\Contracts\NtfyNotification;
use App\Components\Ntfy\Services\MessageBuilder;
use Ntfy\Message as NtfyMessage;
use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification as BaseNotification;

class BackupHasFailedNotification extends BaseNotification implements NtfyNotification
{
    use PullsNotificationConfiguration;

    public function toNtfy($notifiable): NtfyMessage
    {
        $message = [
            __('The backup process has failed.'),

            __('Exception details:'),
            json_encode([
                'message' => $this->event->exception->getMessage(),
                'type' => get_class($this->event->exception),
                'trace' => $this->event->exception->getTrace(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),

            __('Backup Destination Properties:'),
            json_encode($this->backupDestinationProperties()->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ];

        return MessageBuilder::make()
            ->title(__('Backup Has Failed'))
            ->body(implode("\n", $message))
            ->build();
    }
}
