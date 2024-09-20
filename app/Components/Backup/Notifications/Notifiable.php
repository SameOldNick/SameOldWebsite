<?php

namespace App\Components\Backup\Notifications;

use App\Components\Backup\Contracts\NotificationConfigurationProviderInterface;
use Spatie\Backup\Notifications\Notifiable as BaseNotifiable;

class Notifiable extends BaseNotifiable
{
    public function __construct(
        protected readonly NotificationConfigurationProviderInterface $configurationProvider
    ) {}

    public function routeNotificationForMail(): string|array
    {
        return $this->configurationProvider->getMailToEmails();
    }

    public function routeNotificationForSlack(): string
    {
        return $this->configurationProvider->getSlackWebhook();
    }

    public function routeNotificationForDiscord(): string
    {
        return $this->configurationProvider->getDiscordWebhook();
    }

    public function getKey(): int
    {
        return 1;
    }
}
