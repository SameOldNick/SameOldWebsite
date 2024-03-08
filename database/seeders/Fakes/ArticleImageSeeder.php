<?php

namespace Database\Seeders\Fakes;

use App\Models\Article;
use App\Models\File;
use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Article $article = null, int $count = 1, array $options = [], ?User $user = null)
    {
        $options = array_merge([
            'ext' => '.jpg',
            'width' => 1024,
            'height' => 768,
            'public' => true,
        ], $options);

        $images = collect();

        for ($n = 0; $n < $count; $n++) {
            $url = sprintf('https://picsum.photos/%d/%d%s', $options['width'], $options['height'], $options['ext']);
            $response = Http::get($url);

            $metaUrl = sprintf('https://picsum.photos/id/%s/info', $response->header('Picsum-ID'));
            $meta = Http::get($metaUrl)->json();

            $fileName = sprintf('%s%s', Str::uuid(), $options['ext']);

            $path = sprintf('files/%s', $fileName);

            Storage::put($path, $response->body());

            $file = File::createFromFilePath($path, null, true);

            if (! is_null($user)) {
                $file->user()->associate($user);
            }

            $image = Image::create([
                'description' => sprintf('Author: %s'.PHP_EOL.'Source: %s', $meta['author'], $meta['url']),
            ]);

            $image->file()->save($file);

            if (! is_null($article)) {
                $image->articles()->attach($article);
            }

            $images->push($image);
        }

        if (fake()->boolean() && $images->isNotEmpty()) {
            $article->mainImage()->associate($images->random())->save();
        }
    }
}
