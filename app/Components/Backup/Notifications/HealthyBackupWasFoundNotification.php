<?php

namespace App\Components\Backup\Notifications;

use Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification as BaseNotification;
use App\Components\Backup\Concerns\PullsNotificationConfiguration;

class HealthyBackupWasFoundNotification extends BaseNotification
{
    use PullsNotificationConfiguration;
}
