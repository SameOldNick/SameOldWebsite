<?php

namespace App\Components\Backup\Contracts;

use Spatie\Backup\Notifications\Notifiable as BaseNotifiable;

interface NotificationConfigurationProviderInterface
{
    /**
     * Gets the channels for the specified notification channel class.
     *
     * @param class-string $notification
     * @return array
     */
    public function channels(string $notification): array;

    /**
     * Gets email address to send backup notifications to.
     *
     * @return string[]
     */
    public function getMailToEmails(): array;

    /**
     * Gets email to use as from.
     *
     * @return string
     */
    public function getMailFromEmail(): string;

    /**
     * Gets name to use as from.
     *
     * @return ?string
     */
    public function getMailFromName(): ?string;

    /**
     * Gets webhook URL to send Discord notifications to.
     *
     * @return string
     */
    public function getDiscordWebhook(): string;

    /**
     * Gets Discord username
     *
     * @return string
     */
    public function getDiscordUsername(): string;

    /**
     * Gets Discord Avatar URL
     *
     * @return ?string
     */
    public function getDiscordAvatarUrl(): ?string;

    /**
     * Gets webhook URLs to send Slack notifications to.
     *
     * @return string
     */
    public function getSlackWebhook(): string;

    /**
     * Gets Slack username
     *
     * @return string
     */
    public function getSlackUsername(): string;

    /**
     * Gets Slack icon
     *
     * @return ?string
     */
    public function getSlackIcon(): ?string;

    /**
     * Gets Slack channel
     *
     * @return string
     */
    public function getSlackChannel(): string;
}
