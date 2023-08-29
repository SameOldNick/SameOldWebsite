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

    public function mostRecent() {
        return
            Article::published()
                ->sortedByPublishDate()
                    ->limit(5)
                    ->get();
    }

    /**
     * Gets articles with most comments
     *
     * @return void
     */
    public function popular() {
        return
            Article::published()
                ->get()
                    ->popular()
                    ->take(5);
    }

    /**
     * Gets months that had articles published
     *
     * @return void
     */
    public function monthsWithArticles() {
        return
            Article::published()
                ->get()
                    ->groupedByDateTime('Y-m')
                    ->sortKeysDesc()
                    ->keys()
                        ->map(fn ($value) => Carbon::parse($value));
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
