<?php

namespace Database\Seeders\Fakes;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Page;
use App\Models\PageMetaData;

class PageMetaDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $metadata = [
            'homepage' => [
                'name' => fake()->name,
                'headline' => fake()->jobTitle,
                'location' => fake()->country,
                'biography' => fake()->markdown()
            ],
        ];

        foreach ($metadata as $page => $entries) {
            $models = collect($entries)->map(fn ($value, $key) => new PageMetaData(['key' => $key, 'value' => $value]));

            Page::firstWhere(['page' => $page])->metaData()->saveMany($models);
        }
    }
}
