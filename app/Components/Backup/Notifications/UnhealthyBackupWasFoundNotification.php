<?php

namespace App\Components\Backup\Notifications;

use App\Components\Backup\Concerns\PullsNotificationConfiguration;
use App\Components\Ntfy\Services\MessageBuilder;
use Ntfy\Message as NtfyMessage;
use Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification as BaseNotification;

class UnhealthyBackupWasFoundNotification extends BaseNotification
{
    use PullsNotificationConfiguration;

    /**
     * Create an ntfy message representation of the notification.
     */
    public function toNtfy($notifiable): NtfyMessage
    {
        $message = [
            __('An unhealthy backup was detected during the backup process.'),

            __('Failure details:'),
            json_encode([
                'name' => $this->failure()->healthCheck()->name(),
                'unexpected' => $this->failure()->wasUnexpected(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),

            __('Exception details:'),
            json_encode([
                'message' => $this->failure()->exception()->getMessage(),
                'type' => get_class($this->failure()->exception()),
                'trace' => $this->failure()->exception()->getTrace(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),

            __('Backup Destination Properties:'),
            json_encode($this->backupDestinationProperties()->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ];

        return MessageBuilder::make()
            ->title(__('Unhealthy Backup Was Found'))
            ->body(implode("\n", $message))
            ->build();
    }
}
