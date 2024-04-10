<?php

namespace App\View\Components;

use App\Models\Article;
use Illuminate\Support\Carbon;
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
     * @return \App\Models\Collections\ArticleCollection
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
     * @return \App\Models\Collections\ArticleCollection
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
     * @return \Illuminate\Support\Collection<int, Carbon>
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
     * @return \App\Models\Collections\ArticleCollection
     */
    protected function getPublishedArticles()
    {
        return Article::published()->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.main.blog.sidebar');
    }
}
