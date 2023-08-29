<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            'homepage',
            'blog',
            'contact',
        ];

        foreach ($pages as $page) {
            DB::table('pages')->insert(compact('page'));
        }
    }
}
