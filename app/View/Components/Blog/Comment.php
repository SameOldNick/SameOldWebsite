<?php

namespace App\View\Components\Blog;

use App\Models\Article;
use App\Models\Comment as CommentModel;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\View\Component;

class Comment extends Component
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
        public readonly CommentModel $comment,
        ?CommentModel $parent,
        public readonly int $level = 1,
    )
    {
        $this->parent = !is_null($parent) && $parent->exists ? $parent : null;
    }

    /**
     * Gets the children of the comment to display.
     *
     * @return \App\Models\Collections\CommentCollection
     */
    public function children() {
        if ($this->level < 2) {
            return $this->comment->children->sortBy(fn (CommentModel $comment) => $comment->post->created_at);
        } else if ($this->level === 2) {
            return $this->comment->allChildren()->sortBy(fn (CommentModel $comment) => $comment->post->created_at);
        } else {
            return $this->comment->newCollection([]);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.main.blog.comment');
    }
}
