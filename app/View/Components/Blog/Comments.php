<?php

namespace App\View\Components\Blog;

use App\Models\Article;
use App\Models\Comment as CommentModel;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\View\Component;

class Comments extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public readonly Request $request,
        public readonly Article $article
    )
    {
        //
    }

    /**
     * Gets all the comments
     *
     * @return \App\Models\Collections\CommentCollection
     */
    public function comments() {
        // Don't check if they can be viewed here. That is done by the policy.
        return
            $this->article->comments()
                ->parents()
                ->get()
                ->sortBy(fn ($comment) => $comment->post->created_at);
    }

    /**
     * Gets the parent comment being replied to
     *
     * @return ?CommentModel
     */
    public function parent() {
        $parentComment = $this->request->has('parent_comment_id') ? CommentModel::find($this->request->parent_comment_id) : null;

        return ! is_null($parentComment) && $parentComment->article->is($this->article) ? $parentComment : null;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.main.blog.comments');
    }
}
