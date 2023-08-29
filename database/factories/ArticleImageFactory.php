<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
            //
        ];
    }

    /**
     * Creates from picsum.photos website
     *
     * @param array $options
     * @return $this
     */
    public function picsum(array $options = [])
    {
        return $this->afterCreating(function ($model) use ($options) {
            $defaults = [
                'ext' => '.jpg',
                'width' => 1024,
                'height' => 768,
            ];

            $options = array_merge($defaults, $options);

            $url = sprintf('https://picsum.photos/%d/%d%s', $options['width'], $options['height'], $options['ext']);
            $response = Http::get($url);

            $metaUrl = sprintf('https://picsum.photos/id/%s/info', $response->header('Picsum-ID'));
            $meta = Http::get($metaUrl)->json();

            $fileName = sprintf('%s%s', Str::uuid(), $options['ext']);

            $model->description = sprintf('Author: %s'.PHP_EOL.'Source: %s', $meta['author'], $meta['url']);
            $model->file()->save(FileFactory::new()->fromContents($fileName, $response->body(), true)->create());

            $model->save();
        });
    }
}
