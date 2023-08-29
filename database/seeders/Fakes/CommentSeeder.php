<?php

namespace Database\Seeders\Fakes;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $articles = Article::all();

        foreach ($articles as $article) {
            $baseFactory = Comment::factory()->for($article);

            $nestedFactory =
                $baseFactory
                    ->has(
                        $baseFactory->count(fake()->numberBetween(1, 5))
                            ->has(
                                $baseFactory
                                    ->count(fake()->numberBetween(1, 5))
                                    ->has(
                                        $baseFactory->count(fake()->numberBetween(1, 5)),
                                        'children'
                                    ),
                                'children'
                            ),
                        'children'
                    );

            $factory =
                $baseFactory
                    ->has($nestedFactory->count(fake()->numberBetween(1, 5)), 'children')
                    ->has($$nestedFactory->count(fake()->numberBetween(1, 5))->approved(), 'children');
                    //->has($baseFactory->count(fake()->numberBetween(1, 5)), 'children')
                    //->has($baseFactory->count(fake()->numberBetween(1, 5))->approved(), 'children');

            $factory->count(fake()->numberBetween(1, 5))->approved()->create();
            $factory->count(fake()->numberBetween(1, 5))->create();
        }
    }
}
