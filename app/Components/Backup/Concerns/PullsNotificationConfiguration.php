<?php

namespace App\Components\Backup\Concerns;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Spatie\Backup\Notifications\Channels\Discord\DiscordMessage;

trait PullsNotificationConfiguration
{
    use ApplyNotificationConfiguration;

    public function via(): array
    {
        return $this->getConfigurationProvider()->channels(static::class);
    }

    public function toMail(): MailMessage
    {
        return $this->applyConfigurationToMailMessage(parent::toMail());
    }

    public function toSlack(): SlackMessage
    {
        return $this->applyConfigurationToSlackMessage(parent::toSlack());
    }

    public function toDiscord(): DiscordMessage
    {
        return $this->applyConfigurationToDiscordMessage(parent::toDiscord());
    }
}
