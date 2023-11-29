<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            Initial\ProjectSeeder::class,
            Initial\SkillSeeder::class,
            Initial\TechnologySeeder::class,
            Initial\UserSeeder::class,
        ]);
    }
}
