<?php

namespace Database\Factories;

use App\Models\FilesystemConfiguration;
use App\Models\FilesystemConfigurationFTP;
use App\Models\FilesystemConfigurationSFTP;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FilesystemConfiguration>
 */
class FilesystemConfigurationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->slug,
        ];
    }

    public function ftp(): static
    {
        return $this->state([
            'disk_type' => 'ftp',
        ])->configurable(FilesystemConfigurationFTP::factory());
    }

    public function sftp(?string $authType = null): static
    {
        $factory = match ($authType) {
            'password' => FilesystemConfigurationSFTP::factory()->authPassword(),
            'key' => FilesystemConfigurationSFTP::factory()->authKey(),
            default => FilesystemConfigurationSFTP::factory(),
        };

        return $this->state([
            'disk_type' => 'sftp',
        ])->configurable($factory);
    }

    public function configurable(Factory $factory): static
    {
        return $this->afterMaking(function (FilesystemConfiguration $fsConfiguration) use ($factory) {
            $fsConfiguration->configurable()->associate($factory->create());
        });
    }
}
