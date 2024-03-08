<?php

namespace Database\Seeders\Fakes;

use App\Models\Article;
use App\Models\Revision;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articleUsers = User::factory(5)->create();

        $articleFactory = Article::factory(5)->faked(fn () => $articleUsers->random(), Revision::factory(fake()->numberBetween(1, 3)));

        $published = $articleFactory->published()->create();
        $deleted = $articleFactory->deleted()->create();
        $drafted = $articleFactory->create();

        $all = [...$published, ...$deleted, ...$drafted];

        $commentUsers = User::factory(5)->create();

        foreach ($all as $article) {
            $this->callWith(ImageSeeder::class, ['article' => $article, 'count' => fake()->numberBetween(0, 3), 'user' => $article->post->user]);

            $this->callWith(CommentSeeder::class, ['article' => $article, 'users' => $commentUsers]);
        }

        //$this->callWith(CommentSeeder::class, ['article' => $article, 'userFactory' => $userFactory]);
    }
}
