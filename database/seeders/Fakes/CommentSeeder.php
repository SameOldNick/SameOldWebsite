<?php

namespace Database\Seeders\Fakes;

use App\Enums\CommentStatus;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Commenter;
use App\Models\Post;
use App\Models\User;
use Database\Factories\CommenterFactory;
use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class CommentSeeder extends Seeder
{
    protected int $minEach;
    protected int $maxEach;
    protected int $maxDepth;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Article $article, $minEach = 1, $maxEach = 3, $maxDepth = 3)
    {
        $this->minEach = $minEach;
        $this->maxEach = $maxEach;
        $this->maxDepth = $maxDepth;

        $this->createComments($article, null, 1);

    }

    /**
     * Creates comments
     *
     * @param Article $article Article to associate comments with.
     * @param ?Comment $parent Parent comment to associate children with. If null, comments are top-level parents.
     * @param integer $level Level of comments
     * @return void
     */
    protected function createComments(Article $article, ?Comment $parent, int $level) {
        if ($level > $this->getMaxDepth())
            return;

        ['registered' => $registeredCount, 'guest' => $guestCount] = $this->getCommentCount();

        $commentFactory = $this->createCommentFactory($article, $parent, $level);
        $postFactory = $this->createPostFactory($parent);

        $comments = [
            ...$commentFactory->registered(postFactory: $postFactory)->count($registeredCount)->create(),
            ...$commentFactory->guest(postFactory: $postFactory)->count($guestCount)->create()
        ];

        foreach ($comments as $comment) {
            $this->createComments($article, $comment, $level + 1);
        }
    }

    /**
     * Creates CommentFactory for creating comments.
     *
     * @param Article $article
     * @param Comment|null $parent
     * @param integer $level
     * @return CommentFactory
     */
    protected function createCommentFactory(Article $article, ?Comment $parent = null, int $level) {
        $factory = Comment::factory()->for($article);

        if (!is_null($parent))
            $factory = $factory->for($parent, 'parent');

        if ($level < $this->getMaxDepth()) {
            // Parent comments (comment with children) can be approved or locked.
            $factory = $factory->fakedStatus(cases: [CommentStatus::Approved, CommentStatus::Locked]);
        } else if ($level === $this->getMaxDepth()) {
            // Only leaf comments (comments with no children) can have any status
            $factory = $factory->fakedStatus();
        }

        return $factory;
    }

    /**
     * Creates post factory for comments.
     *
     * @param Comment|null $parent
     * @return \Database\Factories\PostFactory
     */
    protected function createPostFactory(?Comment $parent) {
        return Post::factory(fn () => [
            // Set child comments as posted after the parent
            'created_at' => !is_null($parent) ? fake()->dateTimeBetween($parent->post->created_at, 'now') : fake()->dateTimeBetween('-3 years', 'now')
        ]);
    }

    /**
     * Gets the number of registered and guest comments to create.
     *
     * @return array
     */
    protected function getCommentCount() {
        $total = fake()->numberBetween($this->getMinEach(), $this->getMaxEach());
        $registeredCount = fake()->numberBetween($this->getMinEach(), $total);
        $guestCount = $total - $registeredCount;

        return ['registered' => $registeredCount, 'guest' => $guestCount];
    }

    /**
     * Gets minimum number of comments to create at each level.
     *
     * @return integer
     */
    protected function getMinEach(): int {
        return $this->minEach;
    }

    /**
     * Gets maximum number of comments to create at each level.
     *
     * @return integer
     */
    protected function getMaxEach(): int {
        return $this->maxEach;
    }

    /**
     * Gets maximum number of levels to create.
     *
     * @return integer
     */
    protected function getMaxDepth(): int {
        return $this->maxDepth;
    }
}
