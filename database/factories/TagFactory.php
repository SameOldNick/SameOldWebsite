<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'tag' => $this->faker->unique()->word(),
        ];
    }

    /**
     * Indicate that the tag will have an associated slug.
     *
     * @return static
     */
    public function slugged()
    {
        return $this->state(fn () => [
            'slug' => $this->faker->unique()->slug(3),
        ]);
    }
}
