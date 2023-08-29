<?php

namespace Database\Seeders\Fakes;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Tag;

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
