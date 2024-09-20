<?php

namespace App\Components\Backup\Notifications;

use App\Components\Backup\Concerns\PullsNotificationConfiguration;
use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification as BaseNotification;

class BackupWasSuccessfulNotification extends BaseNotification
{
    use PullsNotificationConfiguration;
}
