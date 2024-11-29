<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FilesystemConfigurationFTP>
 */
class FilesystemConfigurationFTPFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'host' => $this->faker->boolean ? $this->faker->unique()->ipv4 : $this->faker->unique()->domainName,
            'port' => $this->faker->boolean(90) ? 21 : $this->faker->numberBetween(1000, 9999),
            'username' => $this->faker->unique()->userName,
            'password' => $this->faker->unique()->password,
            'root' => $this->faker->boolean ? implode('/', $this->faker->words($this->faker->numberBetween(1, 4))) : null,
            'extra' => $this->faker->boolean ? [] : null,
        ];
    }
}
