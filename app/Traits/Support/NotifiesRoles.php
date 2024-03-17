<?php

namespace App\Traits\Support;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;

trait NotifiesRoles
{
    public function notifyRoles($roles, $notification)
    {
        $users = User::getUsersWithRoles(Arr::wrap($roles))->all();

        Notification::send($users, $notification);
    }
}
