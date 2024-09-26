<?php

namespace App\Http\Controllers\Api\Backup;

use App\Http\Controllers\Controller;
use App\Models\BackupConfig;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BackupSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role-manage-backups');
    }

    /**
     * Displays backup settings.
     */
    public function show()
    {
        $keys = [
            'notification_channel',
            'notification_to_email',
            'notification_from_email',
            'notification_from_name',
            'notification_discord_webhook',
            'notification_discord_username',
            'notification_discord_avatar_url',
            'notification_slack_webhook',
            'notification_slack_username',
            'notification_slack_icon',
            'notification_slack_channel',
        ];

        return BackupConfig::whereIn('key', $keys)->get();
    }

    /**
     * Updates backup settings
     */
    public function update(Request $request)
    {
        $channels = $request->collect('notification_channel');

        $validated = $request->validate([
            'notification_channel' => 'required|array',
            'notification_channel.*' => Rule::in(['mail', 'discord', 'slack']),

            'notification_to_email' => [Rule::requiredIf($channels->contains('mail')), 'nullable', 'array'],
            'notification_to_email.*' => 'email',
            'notification_from_email' => [Rule::requiredIf($channels->contains('mail')), 'nullable', 'email'],
            'notification_from_name' => [Rule::requiredIf($channels->contains('mail')), 'nullable', 'string', 'max:255'],

            'notification_discord_webhook' => [Rule::requiredIf($channels->contains('discord')), 'nullable', 'url:http,https', 'max:255'],
            'notification_discord_username' => [Rule::requiredIf($channels->contains('discord')), 'nullable', 'string', 'max:255'],
            'notification_discord_avatar_url' => ['nullable', 'url:http,https', 'max:255'],

            'notification_slack_webhook' => [Rule::requiredIf($channels->contains('slack')), 'nullable', 'url:http,https', 'max:255'],
            'notification_slack_username' => [Rule::requiredIf($channels->contains('slack')), 'nullable', 'string', 'max:255'],
            'notification_slack_icon' => ['nullable', 'string', 'max:255'],
            'notification_slack_channel' => [Rule::requiredIf($channels->contains('slack')), 'nullable', 'string', 'max:255'],
        ]);

        foreach ($validated as $key => $value) {
            $value = is_array($value) ? implode(';', $value) : $value;

            BackupConfig::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return BackupConfig::all();
    }
}
