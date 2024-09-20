<?php

namespace App\Components\Backup\Contracts;

interface NotificationConfigurationProviderInterface
{
    /**
     * Gets the channels for the specified notification channel class.
     *
     * @param  class-string  $notification
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
     */
    public function getMailFromEmail(): string;

    /**
     * Gets name to use as from.
     */
    public function getMailFromName(): ?string;

    /**
     * Gets webhook URL to send Discord notifications to.
     */
    public function getDiscordWebhook(): string;

    /**
     * Gets Discord username
     */
    public function getDiscordUsername(): string;

    /**
     * Gets Discord Avatar URL
     */
    public function getDiscordAvatarUrl(): ?string;

    /**
     * Gets webhook URLs to send Slack notifications to.
     */
    public function getSlackWebhook(): string;

    /**
     * Gets Slack username
     */
    public function getSlackUsername(): string;

    /**
     * Gets Slack icon
     */
    public function getSlackIcon(): ?string;

    /**
     * Gets Slack channel
     */
    public function getSlackChannel(): string;
}
