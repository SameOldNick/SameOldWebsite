<?php

namespace Database\Seeders\Fakes;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::factory()->count(20)->create();
        Tag::factory()->count(20)->slugged()->create();
    }
}
