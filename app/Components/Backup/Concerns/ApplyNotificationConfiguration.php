<?php

namespace App\Components\Backup\Concerns;

use App\Components\Backup\Contracts\NotificationConfigurationProviderInterface;
use Illuminate\Notifications\Messages\MailMessage;
use Spatie\Backup\Notifications\Channels\Discord\DiscordMessage;
use Illuminate\Notifications\Messages\SlackMessage;

trait ApplyNotificationConfiguration
{
    /**
     * Gets the configuration provider.
     *
     * @return NotificationConfigurationProviderInterface
     */
    protected function getConfigurationProvider(): NotificationConfigurationProviderInterface
    {
        return app(NotificationConfigurationProviderInterface::class);
    }

    /**
     * Applies configuration to mail message.
     *
     * @param MailMessage $mailMessage
     * @return MailMessage
     */
    protected function applyConfigurationToMailMessage(MailMessage $mailMessage): MailMessage
    {
        $configurationProvider = $this->getConfigurationProvider();

        return $mailMessage->from($configurationProvider->getMailFromEmail(), $configurationProvider->getMailFromName());
    }

    /**
     * Applies configuration to slack message.
     *
     * @param SlackMessage $slackMessage
     * @return SlackMessage
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
     *
     * @param DiscordMessage $discordMessage
     * @return DiscordMessage
     */
    protected function applyConfigurationToDiscordMessage(DiscordMessage $discordMessage): DiscordMessage
    {
        $configurationProvider = $this->getConfigurationProvider();

        return $discordMessage->from($configurationProvider->getDiscordUsername(), $configurationProvider->getDiscordAvatarUrl());
    }
}
