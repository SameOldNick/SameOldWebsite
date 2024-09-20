<?php

namespace App\Components\Backup\Providers;

use App\Components\Backup\Contracts\NotificationConfigurationProviderInterface;
use Illuminate\Support\Facades\DB;

class DatabaseNotificationConfigurationProvider implements NotificationConfigurationProviderInterface
{
    /**
     * The table storing the configuration.
     */
    protected string $table = 'backup_config';

    /**
     * {@inheritDoc}
     */
    public function channels(string $notification): array
    {
        return $this->getArrayValue($this->createKey('channel'));
    }

    /**
     * {@inheritDoc}
     */
    public function getMailToEmails(): array
    {
        return $this->getArrayValue($this->createKey('to_email'));
    }

    /**
     * {@inheritDoc}
     */
    public function getMailFromEmail(): string
    {
        return $this->getStringValue($this->createKey('from_email'), '');
    }

    /**
     * {@inheritDoc}
     */
    public function getMailFromName(): ?string
    {
        return $this->getStringValue($this->createKey('from_name'));
    }

    /**
     * {@inheritDoc}
     */
    public function getDiscordWebhook(): string
    {
        return $this->getStringValue($this->createKey('discord_webhook'), '');
    }

    /**
     * {@inheritDoc}
     */
    public function getDiscordUsername(): string
    {
        return $this->getStringValue($this->createKey('discord_username'), '');
    }

    /**
     * {@inheritDoc}
     */
    public function getDiscordAvatarUrl(): ?string
    {
        return $this->getStringValue($this->createKey('discord_avatar_url'));
    }

    /**
     * {@inheritDoc}
     */
    public function getSlackWebhook(): string
    {
        return $this->getStringValue($this->createKey('slack_webhook'), '');
    }

    /**
     * {@inheritDoc}
     */
    public function getSlackUsername(): string
    {
        return $this->getStringValue($this->createKey('slack_username'), '');
    }

    /**
     * {@inheritDoc}
     */
    public function getSlackIcon(): ?string
    {
        return $this->getStringValue($this->createKey('slack_icon'));
    }

    /**
     * {@inheritDoc}
     */
    public function getSlackChannel(): string
    {
        return $this->getStringValue($this->createKey('slack_channel'), '');
    }

    /**
     * Gets table name
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Creates table key name
     */
    protected function createKey(string $key): string
    {
        return "notification_{$key}";
    }

    /**
     * Gets array value from table
     */
    protected function getArrayValue(string $key, array $default = []): array
    {
        $row = DB::table($this->getTable())->where('key', $key)->first();

        return $row ? explode(';', $row->value) : $default;
    }

    /**
     * Gets string value or default
     *
     * @param  mixed  $default
     * @return mixed
     */
    protected function getStringValue(string $key, $default = null)
    {
        $row = DB::table($this->getTable())->where('key', $key)->first();

        return $row ? $row->value : $default;
    }
}
