<?php

namespace Database\Seeders\Fakes;

use App\Models\Page;
use App\Models\PageMetaData;
use Illuminate\Database\Seeder;

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
                'biography' => fake()->markdown(),
            ],
        ];

        foreach ($metadata as $page => $entries) {
            $models = collect($entries)->map(fn ($value, $key) => new PageMetaData(['key' => $key, 'value' => $value]));

            Page::firstWhere(['page' => $page])->metaData()->saveMany($models);
        }
    }
}
