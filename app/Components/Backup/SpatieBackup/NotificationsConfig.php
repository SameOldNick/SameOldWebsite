<?php

namespace App\Components\Backup\SpatieBackup;

use App\Components\Backup\Contracts\NotificationConfigurationProviderInterface;
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
                \App\Components\Backup\Notifications\BackupHasFailedNotification::class => static::getChannelsForNotification($provider, \App\Components\Backup\Notifications\BackupHasFailedNotification::class),
                \App\Components\Backup\Notifications\UnhealthyBackupWasFoundNotification::class => static::getChannelsForNotification($provider, \App\Components\Backup\Notifications\UnhealthyBackupWasFoundNotification::class),
                \App\Components\Backup\Notifications\CleanupHasFailedNotification::class => static::getChannelsForNotification($provider, \App\Components\Backup\Notifications\CleanupHasFailedNotification::class),
                \App\Components\Backup\Notifications\BackupWasSuccessfulNotification::class => static::getChannelsForNotification($provider, \App\Components\Backup\Notifications\BackupWasSuccessfulNotification::class),
                \App\Components\Backup\Notifications\HealthyBackupWasFoundNotification::class => static::getChannelsForNotification($provider, \App\Components\Backup\Notifications\HealthyBackupWasFoundNotification::class),
                \App\Components\Backup\Notifications\CleanupWasSuccessfulNotification::class => static::getChannelsForNotification($provider, \App\Components\Backup\Notifications\CleanupWasSuccessfulNotification::class),
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
