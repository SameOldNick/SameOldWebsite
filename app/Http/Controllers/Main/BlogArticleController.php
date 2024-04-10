<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Revision;
use Illuminate\Http\Request;

class BlogArticleController extends Controller
{
    /**
     * Display the specified article.
     *
     * @param Article  $article
     * @return \Illuminate\Contracts\View\View
     */
    public function single(Request $request, Article $article)
    {
        $extra = [];

        /**
         * @var Comment|null
         */
        $parentComment = $request->has('parent_comment_id') ? Comment::find($request->parent_comment_id) : null;

        if (! is_null($parentComment) && $parentComment->article->is($article)) {
            $extra['parentComment'] = $parentComment;
        }

        return $this->createArticleResponse($request, $article, $article->revision, $extra);
    }

    /**
     * Displays the specified revision for article
     *
     * @param Request $request
     * @param Article $article
     * @param Revision $revision
     * @return \Illuminate\Contracts\View\View
     */
    public function singleRevision(Request $request, Article $article, Revision $revision)
    {
        return $this->createArticleResponse($request, $article, $revision);
    }

    /**
     * Creates response that renders article revision
     *
     * @param Request $request
     * @param Article $article
     * @param Revision $revision
     * @param array $extra
     * @return \Illuminate\Contracts\View\View
     */
    protected function createArticleResponse(Request $request, Article $article, Revision $revision, array $extra = [])
    {
        $comments =
            $article
                ->comments()
                ->parents()
                ->approved()
                ->orWhere(function ($query) {
                    $query->owned();
                })
                ->get()
                    ->sortBy(fn ($comment) => $comment->post->created_at);

        $comment = old('comment', $request->cookie("{$article->slug}-comment"));

        return view('main.blog.single', array_merge(compact('article', 'revision', 'comments', 'comment'), $extra));
    }
}
