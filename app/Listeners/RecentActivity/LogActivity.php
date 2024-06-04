<?php

namespace App\Listeners\RecentActivity;

use App\Notifications\Activity;
use App\Traits\Support\NotifiesRoles;

abstract class LogActivity
{
    use NotifiesRoles;

    /**
     * What roles to notify of recent activity.
     *
     * @return string
     */
    protected function getRole()
    {
        return 'admin';
    }

    /**
     * Logs as recent activity
     *
     * @param  RecentActivity  $recentActivity
     * @return $this
     */
    protected function log(Activity $recentActivity)
    {
        $this->notifyRoles($this->getRole(), $recentActivity);

        return $this;
    }
}
