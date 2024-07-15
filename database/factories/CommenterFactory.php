<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commenter>
 */
class CommenterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];
    }

    /**
     * Sets commenter as verified
     *
     * @param \DateTimeInterface|callable|null $dateTime DateTime or callable that returns DateTime. If null, current date/time is used.
     * @return static
     */
    public function verified($dateTime = null) {
        return $this->state(fn () => ['email_verified_at' => value($dateTime) ?: now()]);
    }

    /**
     * Sets commenter as unverified
     *
     * @return static
     */
    public function unverified() {
        return $this->state(['email_verified_at' => null]);
    }
}
