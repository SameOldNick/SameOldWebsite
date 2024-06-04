<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence,
        ];
    }

    public function fakedImage(array $exts = ['jpg', 'png', 'bmp'], $user = null)
    {
        return $this->has(File::factory(1)->uploadedFile(
            fn () => UploadedFile::fake()->image(sprintf('%s.%s', $this->faker->uuid, $this->faker->randomElement($exts)))
        )->for($user ?? User::factory()));
    }

    public function withFile(string $path, string $name, bool $public)
    {
        return $this->has(
            File::factory()->state([
                'path' => $path,
                'name' => $name,
                'is_public' => $public,
            ])
        );
    }

    /**
     * Creates from picsum.photos website
     *
     * @param  array  $options
     * @return $this
     */
    public function picsum(string $path, array $meta, bool $public)
    {
        return $this->state([
            'description' => sprintf('Author: %s'.PHP_EOL.'Source: %s', $meta['author'], $meta['url']),
        ])->has(
            File::factory()->state([
                'path' => $path,
                'name' => null,
                'is_public' => $public,
            ])
        );
    }
}
