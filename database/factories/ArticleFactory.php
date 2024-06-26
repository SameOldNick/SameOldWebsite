<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Revision;
use App\Traits\Database\Factories\CreatesPostable;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    use CreatesPostable;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = sprintf('%s %s', Str::headline($this->faker->unique()->realText(25)), $this->faker->emoji());

        return [
            'title' => $title,
            'slug' => Str::slug($title),
        ];
    }

    /**
     * Configure the factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this;
    }

    public function faked($user, RevisionFactory $revisionFactory)
    {
        return
            $this
                ->hasPostWithUser(is_callable($user) ? $user() : $user)
                ->has($revisionFactory)
                ->afterCreating(function (Article $article) {
                    $revisions = $article->revisions;

                    $article->currentRevision()->associate($revisions->random());

                    // Assigns parent revision to each revision (except first)
                    if ($revisions->count() > 1) {
                        for ($i = 1; $i < $revisions->count(); $i++) {
                            $parent = $revisions->get($i - 1);
                            $current = $revisions->get($i);

                            $current->parentRevision()->associate($parent);

                            $current->save();
                        }
                    }

                    $article->save();
                });
    }

    /**
     * Includes revision(s) with article.
     *
     * @return $this
     */
    public function withRevision(int $count = 1)
    {
        return $this->has(Revision::factory()->count($count), 'revisions');
    }

    /**
     * Sets current revision for article.
     *
     * @return $this
     */
    public function currentRevision(Revision $revision)
    {
        return $this->state([
            'current_revision' => $revision,
        ]);
    }

    /**
     * Indicate that the model should be published.
     *
     * @param  (callable(): DateTime)|DateTime|null  $dateTime  When the article is published. If null, a date between 3 years ago and now is used.
     * @return $this
     */
    public function published($dateTime = null)
    {
        return $this->state(function() use ($dateTime) {
            $dateTime = $dateTime ?? $this->faker->dateTimeBetween('-3 years', 'now');

            return [
                'published_at' => is_callable($dateTime) ? $dateTime() : $dateTime
            ];
        });
    }

    }

    /**
     * Indicate that models should be deleted.
     *
     * @return $this
     */
    public function deleted()
    {
        return $this->afterCreating(function (Article $article) {
            $article->post->delete();
        });
    }
}
