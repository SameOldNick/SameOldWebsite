<?php

namespace App\Components\Backup\Notifications;

use Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification as BaseNotification;
use App\Components\Backup\Concerns\PullsNotificationConfiguration;

class UnhealthyBackupWasFoundNotification extends BaseNotification
{
    use PullsNotificationConfiguration;
}
