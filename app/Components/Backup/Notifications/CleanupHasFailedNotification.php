<?php

namespace App\Components\Backup\Notifications;

use Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification as BaseNotification;
use App\Components\Backup\Concerns\PullsNotificationConfiguration;

class CleanupHasFailedNotification extends BaseNotification
{
    use PullsNotificationConfiguration;
}
