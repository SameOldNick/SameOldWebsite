<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $createdAt = $this->faker->dateTimeBetween('-20 years');

        return [
            'created_at' => $createdAt,
            'updated_at' => $this->faker->boolean() ? $this->faker->dateTimeBetween($createdAt) : null,
        ];
    }

    /**
     * Indicate that the model should be deleted.
     *
     * @return static
     */
    public function deleted()
    {
        return $this->state(function (array $attributes) {
            return [
                'deleted_at' => $this->faker->dateTimeBetween($attributes['created_at'], 'now'),
            ];
        });
    }
}
