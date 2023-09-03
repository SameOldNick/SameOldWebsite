<?php

namespace Database\Factories;

use App\Models\Revision;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Revision>
 */
class RevisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'content' => $this->faker->markdown(),
            'summary' => $this->faker->boolean() ? $this->faker->paragraphs(2, true) : null,
        ];
    }

    /**
     * Indicate that models should be linked to parent revision.
     *
     * @return $this
     */
    public function child()
    {
        return $this->afterMaking(function (Revision $revision) {
            $last = $revision->article->revisions()->latest()->first();

            if (! is_null($last)) {
                $revision->parentRevision()->associate($last);
            }
        });
    }
}
