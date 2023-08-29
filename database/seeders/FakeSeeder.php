<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Database\Seeders\Fakes;

class FakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            Fakes\TagSeeder::class,
            Fakes\ArticleSeeder::class,
            Fakes\ProjectSeeder::class,
            Fakes\PageMetaDataSeeder::class,
        ]);
    }
}
