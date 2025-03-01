<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            Setup\Countries\SlimCountryStateSeeder::class,
            Setup\RoleSeeder::class,
            Setup\PageSeeder::class,
            Setup\ContactPageSettingsSeeder::class,
            Setup\BackupConfigSeeder::class,
        ]);
    }
}
