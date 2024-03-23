<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->boolean() ? $this->faker->name() : null,
            'email' => $this->faker->safeEmail(),
            'email_verified_at' => now(),
            'country_code' => Country::inRandomOrder()->first()->code,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Attaches roles to created user(s).
     *
     * @param array ...$roles Role models or name (as string)
     * @return static
     */
    public function hasRoles(...$roles)
    {
        $roles = collect($roles)->flatten();

        return $this->afterCreating(function ($user) use ($roles) {
            $models = $roles->map(fn ($role) => ($role instanceof Role ? $role : Role::firstWhere(['role' => $role]))->getKey());

            $user->roles()->attach($models->toArray());
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state([
            'email_verified_at' => null,
        ]);
    }
}
