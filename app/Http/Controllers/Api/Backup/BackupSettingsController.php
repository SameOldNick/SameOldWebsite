<?php

namespace App\Http\Controllers\Api\Backup;

use App\Http\Controllers\Controller;
use App\Models\BackupConfig;
use App\Rules\CronExpression;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
        // Fetch current values
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
            'backup_cron',
            'cleanup_cron',
            'backup_disks',
        ];

        $currentValues = BackupConfig::whereIn('key', $keys)->get();

        // Fetch possible values from config
        $possibleValues = [
            'backup_disks' => array_keys(config('filesystems.disks', [])),
        ];

        return response()->json([
            'current_values' => $currentValues,
            'possible_values' => $possibleValues,
        ]);
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

            'backup_cron' => ['nullable', 'string', new CronExpression],
            'cleanup_cron' => ['nullable', 'string', new CronExpression],

            'backup_disks' => 'array',
            'backup_disks.*' => Rule::in(array_keys(config('filesystems.disks', []))),
        ]);

        foreach (Arr::except($validated, ['backup_cron', 'cleanup_cron']) as $key => $value) {
            $value = is_array($value) ? implode(';', $value) : $value;

            BackupConfig::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        foreach (['backup_cron', 'cleanup_cron'] as $key) {
            if (isset($validated[$key])) {
                BackupConfig::updateOrCreate(
                    ['key' => $key],
                    ['value' => $validated[$key]]
                );
            } else {
                BackupConfig::where('key', $key)->delete();
            }
        }

        return BackupConfig::all();
    }
}
