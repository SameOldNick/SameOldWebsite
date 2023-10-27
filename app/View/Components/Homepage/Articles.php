<?php

namespace App\View\Components\Homepage;

use Closure;

use App\Models\Article;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Articles extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    public function articles() {
        return
            Article::published()
                ->latest('published_at')
                ->limit(5)
                ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.homepage.articles');
    }
}
