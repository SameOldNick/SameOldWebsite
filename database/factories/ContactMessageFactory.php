<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->realText,
        ];
    }

    /**
     * Marks the contact message as confirmed.
     *
     * @return static
     */
    public function confirmed()
    {
        return $this->state([
            'confirmed_at' => $this->faker->dateTimeBetween('now', '+1 month'),
        ]);
    }

    /**
     * Marks the contact message as requiring confirmation.
     *
     * @return static
     */
    public function requiresConfirmation()
    {
        return $this->state([
            'expires_at' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
        ]);
    }
}
