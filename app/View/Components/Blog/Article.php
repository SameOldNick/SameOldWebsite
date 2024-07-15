<?php

namespace App\View\Components\Blog;

use App\Models\Article as ArticleModel;
use App\Models\Revision;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\View\Component;

class Article extends Component
{
    protected readonly ?Revision $revision;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public readonly Request $request,
        public readonly ArticleModel $article,
        ?Revision $revision = null,
        public readonly bool $preview = false,
    ) {
        $this->revision = $revision && $revision->exists ? $revision : null;
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
     * Gets the article revision
     *
     * @return Revision
     */
    public function revision()
    {
        return $this->revision ?? $this->article->revision;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return $this->preview ? view('components.main.blog.article-preview') : view('components.main.blog.article');
    }
}
