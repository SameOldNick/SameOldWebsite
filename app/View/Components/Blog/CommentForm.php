<?php

namespace App\View\Components\Blog;

use App\Models\Article;
use App\Models\Comment as CommentModel;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\View\Component;

class CommentForm extends Component
{
    /**
     * Comment being replied to
     *
     * @var CommentModel|null
     */
    public readonly ?CommentModel $parent;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public readonly Request $request,
        public readonly Article $article,
        ?CommentModel $parent = null,
    )
    {
        // Laravel will try to fill in Comment model, so set it to null if it doesn't exist
        $this->parent = !is_null($parent) && $parent->exists ? $parent : null;
    }

    /**
     * Gets the comment content
     *
     * @return ?string
     */
    public function content() {
        // TODO: Set cookie
        return old('comment', $this->request->cookie("{$this->article->slug}-comment"));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return !is_null($this->parent) ? view('components.main.blog.comment-form-reply') : view('components.main.blog.comment-form');
    }
}
