<?php

namespace Database\Seeders\Fakes;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Article $article)
    {
        $baseFactory = Comment::factory()->hasPostWithUser()->for($article);

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
                ->has($nestedFactory->count(fake()->numberBetween(1, 5))->approved(), 'children');
        //->has($baseFactory->count(fake()->numberBetween(1, 5)), 'children')
        //->has($baseFactory->count(fake()->numberBetween(1, 5))->approved(), 'children');

        $factory->count(fake()->numberBetween(1, 5))->approved()->create();
        $factory->count(fake()->numberBetween(1, 5))->create();
    }
}
