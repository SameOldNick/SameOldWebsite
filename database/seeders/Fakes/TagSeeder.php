<?php

namespace Database\Seeders\Fakes;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Tag;

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
