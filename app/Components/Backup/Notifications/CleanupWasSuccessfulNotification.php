<?php

namespace App\Components\Backup\Notifications;

use Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification as BaseNotification;
use App\Components\Backup\Concerns\PullsNotificationConfiguration;

class CleanupWasSuccessfulNotification extends BaseNotification
{
    use PullsNotificationConfiguration;
}
