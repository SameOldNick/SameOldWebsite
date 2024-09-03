<?php

namespace Tests\Browser\Pages\Blog;

use App\Models\Article as ArticleModel;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class Article extends Page
{
    public function __construct(
        public readonly ArticleModel $article
    ) {}

    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return route('blog.single', ['article' => $this->article], false);
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@commentForm' => 'form#commentForm',
        ];
    }
}
