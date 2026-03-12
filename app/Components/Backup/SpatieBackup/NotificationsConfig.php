<?php

namespace App\Components\Backup\SpatieBackup;

use App\Components\Backup\Contracts\NotificationConfigurationProviderInterface;
use App\Components\Backup\Notifications\BackupHasFailedNotification;
use App\Components\Backup\Notifications\BackupWasSuccessfulNotification;
use App\Components\Backup\Notifications\CleanupHasFailedNotification;
use App\Components\Backup\Notifications\CleanupWasSuccessfulNotification;
use App\Components\Backup\Notifications\HealthyBackupWasFoundNotification;
use App\Components\Backup\Notifications\UnhealthyBackupWasFoundNotification;
use Spatie\Backup\Config\NotificationDiscordConfig;
use Spatie\Backup\Config\NotificationMailConfig;
use Spatie\Backup\Config\NotificationsConfig as BaseNotificationsConfig;
use Spatie\Backup\Config\NotificationSlackConfig;

class NotificationsConfig extends BaseNotificationsConfig
{
    /**
     * @param  array<class-string<BaseNotification>, array<string>>  $notifications
     * @param  class-string<Notifiable>  $notifiable
     */
    protected function __construct(
        public array $notifications,
        public string $notifiable,
        public NotificationMailConfig $mail,
        public NotificationSlackConfig $slack,
        public ?NotificationDiscordConfig $discord,
        public ?NotificationNtfyConfig $ntfy,
    ) {
        parent::__construct(
            notifications: $notifications,
            notifiable: $notifiable,
            mail: $mail,
            slack: $slack,
            discord: $discord,
        );
    }

    /**
     * Creates NotificationsConfig from the specified provider.
     */
    public static function fromProvider(NotificationConfigurationProviderInterface $provider): self
    {
        return new self(
            notifications: [
                BackupHasFailedNotification::class => static::getChannelsForNotification($provider, BackupHasFailedNotification::class),
                UnhealthyBackupWasFoundNotification::class => static::getChannelsForNotification($provider, UnhealthyBackupWasFoundNotification::class),
                CleanupHasFailedNotification::class => static::getChannelsForNotification($provider, CleanupHasFailedNotification::class),
                BackupWasSuccessfulNotification::class => static::getChannelsForNotification($provider, BackupWasSuccessfulNotification::class),
                HealthyBackupWasFoundNotification::class => static::getChannelsForNotification($provider, HealthyBackupWasFoundNotification::class),
                CleanupWasSuccessfulNotification::class => static::getChannelsForNotification($provider, CleanupWasSuccessfulNotification::class),
            ],
            notifiable: \App\Components\Backup\Notifications\Notifiable::class,
            mail: NotificationMailConfig::fromArray([
                'to' => $provider->getMailToEmails(),
                'from' => [
                    'address' => $provider->getMailFromEmail(),
                    'name' => $provider->getMailFromName(),
                ],
            ]),
            slack: NotificationSlackConfig::fromArray([
                'webhook_url' => $provider->getSlackWebhook(),
                'channel' => $provider->getSlackChannel(),
                'username' => $provider->getSlackUsername(),
                'icon' => $provider->getSlackIcon(),
            ]),
            discord: NotificationDiscordConfig::fromArray([
                'webhook_url' => $provider->getDiscordWebhook(),
                'username' => $provider->getDiscordUsername(),
                'avatar_url' => $provider->getDiscordAvatarUrl(),
            ]),
            ntfy: NotificationNtfyConfig::fromArray([
                'topic' => $provider->getNtfyTopic(),
            ])
        );
    }

    /**
     * Gets the channels for the specified notification channel class.
     *
     * @param  class-string  $notificationClass
     */
    public static function getChannelsForNotification(NotificationConfigurationProviderInterface $provider, string $notificationClass): array
    {
        return $provider->channels($notificationClass);
    }
}
