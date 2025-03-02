<?php

namespace Tests\Feature\Traits;

use App\Models\Article;

trait CreatesArticle
{
    public ?Article $article = null;

    /**
     * Creates the article.
     *
     * @return void
     */
    public function setUpCreatesArticle()
    {
        if (is_null($this->article)) {
            $this->article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->published()->create();
        }
    }
}
