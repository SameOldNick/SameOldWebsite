<?php

namespace Database\Seeders\Fakes;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        Notification::factory()->count(20)->notifiables($users->all())->messageNotification()->create();
        Notification::factory()->count(20)->notifiables($users->all())->securityAlert()->create();
    }
}
