<?php

namespace Database\Seeders\Fakes;

use App\Models\Article;
use App\Models\Revision;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($user = null)
    {
        $revisionFactory = Revision::factory(fake()->numberBetween(1, 3));

        $factory =
            Article::factory()
                ->hasPostWithUser($user)
                ->has($revisionFactory)
                ->afterCreating(function (Article $article) {
                    $article->tags()->attach(Tag::all()->random(fake()->numberBetween(1, 3)));

                    $this->callWith(ArticleImageSeeder::class, ['article' => $article, 'count' => fake()->numberBetween(0, 3), 'options' => [], 'user' => $article->post->user]);

                    $revisions = $article->revisions;

                    $article->currentRevision()->associate($revisions->random());

                    // Assigns parent revision to each revision (except first)
                    if ($revisions->count() > 1) {
                        for ($i = 1; $i < $revisions->count(); $i++) {
                            $parent = $revisions->get($i - 1);
                            $current = $revisions->get($i);

                            $current->parentRevision()->associate($parent);

                            $current->save();
                        }
                    }

                    $article->save();
                });

        $factory->count(5)->published()->create();

        $factory->count(5)->deleted()->create();

        $factory->count(5)->create();
    }
}
