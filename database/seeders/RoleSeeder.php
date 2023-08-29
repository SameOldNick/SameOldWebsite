<?php

namespace Database\Seeders;

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
        $roles = [
            'admin',

            'create_posts',
            'read_posts',
            'update_posts',
            'delete_posts',
            'publish_posts',

            'create_comments',
            'read_comments',
            'update_comments',
            'delete_comments',
            'approve_comments',
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert(compact('role'));
        }
    }
}
