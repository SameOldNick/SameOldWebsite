<?php

namespace Database\Seeders\Initial;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(User $user, $roles = null)
    {
        $roles = $roles ?? Role::all();

        $user->roles()->attach($roles);
    }
}
