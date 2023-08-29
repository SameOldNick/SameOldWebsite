<?php

namespace App\Http\Controllers\Main;

use App\Components\SweetAlert\SweetAlertBuilder;
use App\Components\SweetAlert\SweetAlerts;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostCommentRequest;
use App\Models\Article;
use App\Models\Comment;

class BlogCommentController extends Controller
{
    /**
     * Processes submitted comment
     *
     * @param PostCommentRequest $request
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function comment(SweetAlerts $swal, PostCommentRequest $request, Article $article)
    {
        $this->authorize('create', [Comment::class, $article]);

        $comment =
            Comment::createWithPost(function (Comment $comment) use ($request, $article) {
                $comment->fill(['title' => $request->title, 'comment' => $request->comment]);

                $comment->article()->associate($article);

                if (! config('blog.comments.require_approval', true)) {
                    $comment->approved_at = now();
                }
            });

        $swal->success(function (SweetAlertBuilder $builder) use ($comment) {
            $builder
                ->title('Success')
                ->text($comment->isApproved() ? trans('blog.comments.submitted') : trans('blog.comments.awaiting_approval'));
        });

        return redirect()->route('blog.single', compact('article'));
    }

    /**
     * Processes submitted reply comment
     *
     * @param PostCommentRequest $request
     * @param Article $article
     * @param Comment $parent
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function replyTo(SweetAlerts $swal, PostCommentRequest $request, Article $article, Comment $parent)
    {
        abort_if(! $parent->article->is($article), 404);
        $this->authorize('reply-to', $parent);

        $comment =
            Comment::createWithPost(function (Comment $comment) use ($request, $article, $parent) {
                $comment->fill(['title' => $request->title, 'comment' => $request->comment]);

                $comment->parent()->associate($parent);
                $comment->article()->associate($article);

                if (! config('blog.comments.require_approval', true)) {
                    $comment->approved_at = now();
                }
            });

        $swal->success(function (SweetAlertBuilder $builder) use ($comment) {
            $builder
                ->title('Success')
                ->text($comment->isApproved() ? trans('blog.comments.submitted') : trans('blog.comments.awaiting_approval'));
        });

        return redirect()->route('blog.single', compact('article'));
    }
}
