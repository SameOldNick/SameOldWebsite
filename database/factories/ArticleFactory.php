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

    /**
     * Includes revision(s) with article.
     *
     * @param int $count
     * @return $this
     */
    public function withRevision(int $count = 1)
    {
        return $this->has(Revision::factory()->count($count), 'revisions');
    }

    /**
     * Sets current revision for article.
     *
     * @param Revision $revision
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
     * @param DateTime $dateTime When the article is published. If null, a date between 3 years ago and now is used.
     * @return $this
     */
    public function published(DateTime $dateTime = null)
    {
        return $this->state([
            'published_at' => $dateTime ?? $this->faker->dateTimeBetween('-3 years', 'now'),
        ]);
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
