<?php

namespace App\Traits\Support;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;

trait NotifiesRoles
{
    /**
     * Sends notification to users with roles.
     *
     * @param array $roles The roles the users must have
     * @param mixed $notification
     * @return array Users that were notified
     */
    public function notifyRoles($roles, $notification)
    {
        $users = User::getUsersWithRoles(Arr::wrap($roles))->all();

        Notification::send($users, $notification);

        return $users;
    }
}
