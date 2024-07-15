<?php

namespace App\View\Components\Homepage;

use App\Models\Article as ArticleModel;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Article extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public readonly ArticleModel $article,
    ) {
        //
    }

    /**
     * Gets the total number of viewable comments
     *
     * @return int
     */
    public function totalComments()
    {
        return $this->article->comments->viewable()->count();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.homepage.article');
    }
}
