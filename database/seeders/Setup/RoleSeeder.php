<?php

namespace Database\Seeders\Setup;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = config('roles.roles', []);

        foreach ($roles as $role) {
            DB::table('roles')->insert(['role' => $role['id']]);
        }
    }
}
