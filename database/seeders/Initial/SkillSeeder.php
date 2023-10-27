<?php

namespace Database\Seeders\Initial;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            [
                'icon' => 'fas-code',
                'skill' => 'Web Development',
            ],
            [
                'icon' => 'fas-graduation-cap',
                'skill' => 'Education',
            ],
            [
                'icon' => 'fas-mobile-alt',
                'skill' => 'Mobile Development',
            ],
        ];

        foreach ($skills as $skill) {
            Skill::create($skill);
        }
    }
}
