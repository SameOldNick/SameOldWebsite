<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
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
        return $this->has(Post::factory(1)->for(User::factory()));
    }

    /**
     * Indicate that the model should be published.
     *
     * @return $this
     */
    public function published()
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => $this->faker->dateTimeBetween('-3 years', 'now'),
            ];
        });
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
