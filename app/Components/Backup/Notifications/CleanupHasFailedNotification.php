<?php

namespace App\Components\Backup\Notifications;

use App\Components\Backup\Concerns\PullsNotificationConfiguration;
use Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification as BaseNotification;

class CleanupHasFailedNotification extends BaseNotification
{
    use PullsNotificationConfiguration;
}
