<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

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
        Artisan::call('setup:countries', ['--test' => true]);

        $this->call([
            Setup\RoleSeeder::class,
            Setup\PageSeeder::class,
            Setup\ContactPageSettingsSeeder::class,
            Setup\BackupConfigSeeder::class,
        ]);
    }
}
