<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            Fakes\ArticleSeeder::class,
            Fakes\ProjectSeeder::class,
            Fakes\PageMetaDataSeeder::class,
            Fakes\ContactMessageSeeder::class,
            Fakes\TagSeeder::class,
            Fakes\NotificationSeeder::class,
        ]);
    }
}
