<?php

namespace App\Components\Backup\Notifications;

use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification as BaseNotification;
use App\Components\Backup\Concerns\PullsNotificationConfiguration;

class BackupWasSuccessfulNotification extends BaseNotification
{
    use PullsNotificationConfiguration;
}
