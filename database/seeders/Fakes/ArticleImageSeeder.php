<?php

namespace Database\Seeders\Fakes;

use App\Models\Article;
use App\Models\ArticleImage;
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
    public function run(Article $article = null, int $count = 1, array $options = [])
    {
        $defaults = [
            'ext' => '.jpg',
            'width' => 1024,
            'height' => 768,
            'public' => true,
        ];

        $options = array_merge($defaults, $options);

        for ($n = 0; $n < $count; $n++) {
            $url = sprintf('https://picsum.photos/%d/%d%s', $options['width'], $options['height'], $options['ext']);
            $response = Http::get($url);

            $metaUrl = sprintf('https://picsum.photos/id/%s/info', $response->header('Picsum-ID'));
            $meta = Http::get($metaUrl)->json();

            $fileName = sprintf('%s%s', Str::uuid(), $options['ext']);

            $path = sprintf('files/%s', $fileName);

            Storage::put($path, $response->body());

            ArticleImage::factory()->for($article)->picsum($path, $meta, true)->create();
        }
    }
}
