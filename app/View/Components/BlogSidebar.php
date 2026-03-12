<?php

namespace App\View\Components;

use App\Models\Article;
use App\Models\Collections\ArticleCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class BlogSidebar extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Gets the most recent articles.
     *
     * @return ArticleCollection
     */
    public function mostRecent()
    {
        return
            Article::published()
                ->sortedByPublishDate()
                ->limit(5)
                ->get();
    }

    /**
     * Gets articles with most comments
     *
     * @return ArticleCollection
     */
    public function popular()
    {
        return
            $this->getPublishedArticles()
                ->popular()
                ->take(5);
    }

    /**
     * Gets months that had articles published
     *
     * @return Collection<int, Carbon>
     */
    public function monthsWithArticles()
    {
        return
            $this->getPublishedArticles()
                ->groupedByDateTime('Y-m')
                ->sortKeysDesc()
                ->keys()
                ->map(fn ($value) => Carbon::parse($value));
    }

    /**
     * Gets published articles.
     *
     * @return ArticleCollection
     */
    protected function getPublishedArticles()
    {
        return Article::published()->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|\Closure|string
     */
    public function render()
    {
        return view('components.main.blog.sidebar');
    }
}
