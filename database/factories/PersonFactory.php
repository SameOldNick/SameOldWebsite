<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => null, // Set by user() state or left as null for guests
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
        ];
    }

    /**
     * Indicate that the Person is associated with a registered User.
     *
     * @param mixed $user User to associate. If null, a new user is created. (default: null)
     * @return static
     */
    public function user($user = null)
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user ?? User::factory(),
            'name' => null,  // Clear out name since it will be taken from User
            'email' => null, // Clear out email since it will be taken from User
        ]);
    }

    /**
     * Indicate that the Person is a guest (without a registered User).
     *
     * @return static
     */
    public function guest()
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null, // Ensure no user is associated
        ]);
    }

    /**
     * Indicates the guest is verified.
     *
     * @param  \DateTimeInterface|callable|null  $dateTime  DateTime or callable that returns DateTime. If null, current date/time is used.
     * @return static
     */
    public function verified($dateTime = null)
    {
        return $this->state(fn () => ['email_verified_at' => value($dateTime) ?: now()]);
    }

    /**
     * Indicates the guest is unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(['email_verified_at' => null]);
    }
}
