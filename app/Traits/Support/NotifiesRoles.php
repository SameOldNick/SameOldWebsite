<?php

namespace App\Traits\Support;

use App\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;

trait NotifiesRoles
{
    public function notifyRoles($roles, $notification)
    {
        foreach (Arr::wrap($roles) as $role) {
            $users = Role::firstWhere(['role' => $role])->users;

            Notification::send($users, $notification);
        }
    }
}
