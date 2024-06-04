<?php

namespace Database\Seeders\Fakes;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Article $article, Collection $users, $minEach = 1, $maxEach = 3, $maxDepth = 3)
    {
        $userFactory = $userFactory ?? User::factory();

        $commentFactory =
            Comment::factory()->hasPostWithUser($users->random())->for($article)
                ->has($this->createNestedComments($article, $users, $minEach, $maxEach, $maxDepth), 'children')
                ->has($this->createNestedComments($article, $users, $minEach, $maxEach, $maxDepth)->approved(), 'children');

        $commentFactory->count(fake()->numberBetween($minEach, $maxEach))->approved()->create();
        $commentFactory->count(fake()->numberBetween($minEach, $maxEach))->create();
    }
    /**
     * Create nested factory recursively.
     *
     * @param  Article $article
     * @param  int  $minEach Minimum number of comments at each level.
     * @param  int  $maxEach Maximum number of comments at each level.
     * @param  int  $maxDepth Maximum level
     * @param  int  $depth
     * @return mixed
     */
    private function createNestedComments(Article $article, Collection $users, $minEach, $maxEach, $maxDepth, $depth = 0)
    {
        $factory = Comment::factory(fake()->numberBetween($minEach, $maxEach))->hasPostWithUser($users->random())->for($article);

        if ($depth >= $maxDepth) {
            return $factory;
        }

        return $factory->has(
            $this->createNestedComments($article, $users, $minEach, $maxEach, $maxDepth, $depth + 1),
            'children'
        );
    }
}
