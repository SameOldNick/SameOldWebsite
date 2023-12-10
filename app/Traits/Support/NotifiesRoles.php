<?php

namespace App\Traits\Support;

use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use App\Models\Role;

trait NotifiesRoles
{
    public function notifyRoles($roles, $notification) {
        foreach (Arr::wrap($roles) as $role) {
            $users = Role::firstWhere(['role' => $role])->users;

            Notification::send($users, $notification);
        }
    }
}
