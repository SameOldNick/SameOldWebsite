<?php

namespace Database\Seeders\Fakes;

use App\Models\Article;
use App\Models\Commenter;
use App\Models\Revision;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(int $count = 5, $seedComments = true)
    {
        $articleUsers = User::factory($count)->create();

        $articleFactory = Article::factory($count)->faked(fn () => $articleUsers->random(), Revision::factory(fake()->numberBetween(1, 3)));

        $articles = [
            ...$articleFactory->published()->create(),
            ...$articleFactory->deleted()->create(),
            ...$articleFactory->create(),
        ];

        if ($seedComments) {
            foreach ($articles as $article) {
                $this->callWith(ArticleImageSeeder::class, [
                    'article' => $article,
                    'count' => fake()->numberBetween(0, 3),
                    'user' => $article->post->user,
                    'mainImage' => fake()->boolean
                ]);

                $this->callWith(CommentSeeder::class, ['article' => $article]);
            }
        }

    }
}
