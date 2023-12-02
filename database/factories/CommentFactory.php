<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    use CreatesPostable;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->boolean() ? $this->faker->words(3, true) : null,
            'comment' => $this->faker->paragraphs(2, true),
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
     * Indicate that the model should be approved.
     *
     * @return static
     */
    public function approved($user = null)
    {
        return $this->for($user ?? User::factory(), 'approvedBy')->state(function (array $attributes) {
            return [
                'approved_at' => $this->faker->dateTimeBetween('-3 years', 'now'),
            ];
        });
    }
}
