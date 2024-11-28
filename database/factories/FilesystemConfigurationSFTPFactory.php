<?php

namespace Database\Factories;

use App\Models\FilesystemConfiguration;
use App\Models\FilesystemConfigurationFTP;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FilesystemConfigurationSFTP>
 */
class FilesystemConfigurationSFTPFactory extends Factory
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
            'port' => $this->faker->boolean(90) ? 22 : $this->faker->numberBetween(1000, 9999),
            'username' => $this->faker->unique()->userName,
            'extra' => $this->faker->boolean ? [] : null,
        ];
    }

    public function authPassword()
    {
        return $this->state(fn() => [
            'password' => $this->faker->unique()->password,
        ]);
    }

    public function authKey()
    {
        return $this->state(fn() => [
            'private_key' => $this->faker->unique()->sha256,
            'passphrase' => $this->faker->boolean ? $this->faker->unique()->password : null,
        ]);
    }
}
