<?php

namespace App\Components\Backup\Concerns;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Ntfy\Message as NtfyMessage;
use Spatie\Backup\Notifications\Channels\Discord\DiscordMessage;

trait PullsNotificationConfiguration
{
    use ApplyNotificationConfiguration;

    /**
     * Get the notification channels.
     */
    public function via(): array
    {
        return $this->getConfigurationProvider()->channels(static::class);
    }

    /**
     * Apply configuration to each mail notification channel message.
     */
    public function toMail(): MailMessage
    {
        return $this->applyConfigurationToMailMessage(parent::toMail());
    }

    /**
     * Apply configuration to each Slack notification channel message.
     */
    public function toSlack(): SlackMessage
    {
        return $this->applyConfigurationToSlackMessage(parent::toSlack());
    }

    /**
     * Apply configuration to each Discord notification channel message.
     */
    public function toDiscord(): DiscordMessage
    {
        return $this->applyConfigurationToDiscordMessage(parent::toDiscord());
    }

    /**
     * Apply configuration to each Ntfy notification channel message.
     */
    public function toNtfy($notifiable): NtfyMessage
    {
        return $this->applyConfigurationToNtfyMessage(parent::toNtfy($notifiable));
    }
}
