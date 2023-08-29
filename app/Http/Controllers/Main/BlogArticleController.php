<?php

namespace App\Http\Controllers\Main;


use App\Http\Controllers\Controller;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Revision;
use App\Models\Post;
use Illuminate\Http\Request;

class BlogArticleController extends Controller
{
    /**
     * Display the specified article.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function single(Request $request, Article $article)
    {
        $extra = [];

        $parentComment = $request->has('parent_comment_id') ? Comment::find($request->parent_comment_id) : null;

        if (!is_null($parentComment) && $parentComment->article->is($article))
            $extra['parentComment'] = $parentComment;

        return $this->createArticleResponse($request, $article, $article->revision(), $extra);
    }

    /**
     * Displays the specified revision for article
     *
     * @param Request $request
     * @param Article $article
     * @param Revision $revision
     * @return \Illuminate\Http\Response
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
                ->orWhere(function($query) {
                    $query->owned();
                })
                ->get()
                    ->sortBy(fn ($comment) => $comment->post->created_at);

        $comment = old('comment', $request->cookie("{$article->slug}-comment"));

        return view('main.blog.single', array_merge(compact('article', 'revision', 'comments', 'comment'), $extra));
    }


}
