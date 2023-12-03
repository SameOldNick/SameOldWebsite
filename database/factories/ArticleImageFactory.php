<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use App\Models\File;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ArticleImage>
 */
class ArticleImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [

        ];
    }

    /**
     * Creates from picsum.photos website
     *
     * @param array $options
     * @return $this
     */
    public function picsum(string $path, array $meta, bool $public)
    {
        return $this->state([
            'description' => sprintf('Author: %s'.PHP_EOL.'Source: %s', $meta['author'], $meta['url'])
        ])->has(
            File::factory()->state([
                'path' => $path,
                'name' => null,
                'is_public' => $public,
            ])
        );
    }
}
