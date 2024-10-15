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
     * @param  array  $roles  The roles the users must have
     * @param  mixed  $notification The notification or callback that is invoked with the User instance.
     * @return User[] Users that were notified
     */
    public function notifyRoles($roles, $notification)
    {
        $users = User::getUsersWithRoles(Arr::wrap($roles))->all();

        if (is_callable($notification)) {
            foreach ($users as $user) {
                Notification::send($user, $notification($user));
            }
        } else {
            Notification::send($users, $notification);
        }

        return $users;
    }
}
