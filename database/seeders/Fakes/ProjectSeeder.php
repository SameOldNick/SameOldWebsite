<?php

namespace Database\Seeders\Fakes;

use App\Models\Project;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::factory()
            ->count(10)
            ->afterCreating(function (Project $project) {
                $project->tags()->attach(Tag::all()->random(5));
            })
            ->create();
    }
}
