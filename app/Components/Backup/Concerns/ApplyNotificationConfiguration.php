<?php

namespace App\Components\Backup\Concerns;

use App\Components\Backup\Contracts\NotificationConfigurationProviderInterface;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\Backup\Notifications\Channels\Discord\DiscordMessage;

trait ApplyNotificationConfiguration
{
    /**
     * Gets the configuration provider.
     */
    protected function getConfigurationProvider(): NotificationConfigurationProviderInterface
    {
        return app(NotificationConfigurationProviderInterface::class);
    }

    /**
     * Applies configuration to mail message.
     */
    protected function applyConfigurationToMailMessage(MailMessage $mailMessage): MailMessage
    {
        $configurationProvider = $this->getConfigurationProvider();

        return $mailMessage->from($configurationProvider->getMailFromEmail(), $configurationProvider->getMailFromName());
    }

    /**
     * Applies configuration to slack message.
     */
    protected function applyConfigurationToSlackMessage(SlackMessage $slackMessage): SlackMessage
    {
        $configurationProvider = $this->getConfigurationProvider();

        return $slackMessage
            ->from($configurationProvider->getSlackUsername(), $configurationProvider->getSlackIcon())
            ->to($configurationProvider->getSlackChannel());
    }

    /**
     * Applies configuration to discord message.
     */
    protected function applyConfigurationToDiscordMessage(DiscordMessage $discordMessage): DiscordMessage
    {
        $configurationProvider = $this->getConfigurationProvider();

        return $discordMessage->from($configurationProvider->getDiscordUsername(), $configurationProvider->getDiscordAvatarUrl());
    }
}
