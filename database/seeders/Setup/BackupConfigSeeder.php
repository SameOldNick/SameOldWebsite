<?php

namespace Database\Seeders\Setup;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackupConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gets existing configuration from file.
        $defaults = [
            'notification_channel' => ['mail'],

            'notification_to_email' => [config('backup.notifications.mail.to')],
            'notification_from_email' => config('backup.notifications.mail.from.address', config('mail.from.address')),
            'notification_from_name' => config('backup.notifications.mail.from.name', config('mail.from.name')),
            'notification_discord_webhook' => config('backup.notifications.discord.webhook_url'),
            'notification_discord_username' => config('backup.notifications.discord.username'),
            'notification_discord_avatar_url' => config('backup.notifications.discord.avatar_url'),
            'notification_slack_webhook' => config('backup.notifications.slack.webhook_url'),
            'notification_slack_username' => config('backup.notifications.slack.username'),
            'notification_slack_icon' => config('backup.notifications.slack.icon'),
            'notification_slack_channel' => config('backup.notifications.slack.channel'),
        ];

        foreach ($defaults as $key => $value) {
            $value = is_array($value) ? implode(';', $value) : $value;

            DB::table('backup_config')->insert([
                'key' => $key,
                'value' => $value ?? '',
            ]);
        }
    }
}
