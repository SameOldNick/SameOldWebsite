<?php

namespace Database\Seeders\Fakes;

use App\Models\Article;
use App\Models\ArticleImage;
use App\Models\Revision;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $factory =
            Article::factory()
                ->recycle(User::find(1))
                ->has(Revision::factory()->count(5), 'revisions')
                ->has(ArticleImage::factory(3)->picsum(), 'images')
                ->afterCreating(function (Article $article) {
                    $article->tags()->attach(Tag::all()->random(5));

                    $revisions = $article->revisions;

                    if (fake()->boolean()) {
                        $article->currentRevision()->associate($revisions->random());
                    } else {
                        $article->currentRevision()->associate($revisions->last());
                    }

                    // Assigns parent revision to each revision (except first)
                    if ($revisions->count() > 1) {
                        for ($i = 1; $i < $revisions->count(); $i++) {
                            $parent = $revisions->get($i - 1);
                            $current = $revisions->get($i);

                            $current->parentRevision()->associate($parent);

                            $current->save();
                        }
                    }

                    if (fake()->boolean()) {
                        $images = $article->images;
                        $article->mainImage()->associate($images->random());
                    }

                    $article->save();
                });

        $factory->count(10)->published()->create();

        $factory->count(10)->deleted()->create();

        $factory->count(10)->create();
    }
}
