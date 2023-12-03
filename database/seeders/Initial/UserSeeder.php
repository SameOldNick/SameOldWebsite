<?php

namespace Database\Seeders\Initial;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $user = User::factory()->create([
            'uuid' => '52cb7f81-253f-4783-8bee-1135d6b13e64',
            'name' => 'Same Old Nick',
            'email' => 'nick58@gmail.com',
            'country_code' => 'CAN',
            'password' => '$2y$10$2d5haer/8zZVkF5l.u8ZAuUs0RV.h3hKlKV1BWTBvLsnL6eFZF1a2',
        ]);

        $user->roles()->attach(Role::firstWhere('role', 'admin'));
    }
}
