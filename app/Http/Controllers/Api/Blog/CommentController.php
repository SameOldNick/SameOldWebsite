<?php

namespace App\Http\Controllers\Api\Blog;

use App\Enums\CommentStatus as CommentStatusEnum;
use App\Events\Comments\CommentRemoved;
use App\Events\Comments\CommentStatusChanged;
use App\Events\Comments\CommentUpdated;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentCollection;
use App\Models\Article;
use App\Models\Comment;
use App\Models\CommentStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role-manage-comments');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'show' => [
                'sometimes',
                Rule::enum(CommentStatusEnum::class),
            ],
            'article' => [
                'sometimes',
                'numeric',
                Rule::exists(Article::class, 'id'),
            ],
            'user' => [
                'sometimes',
                'numeric',
                Rule::exists(User::class, 'id'),
            ],
            'commenter' => [
                'sometimes',
                'array',
            ],
            'commenter.name' => [
                'sometimes',
                'string',
            ],
            'commenter.email' => [
                'sometimes',
                'string',
            ],
        ]);

        $query = Comment::with(['article' => fn(BelongsTo $belongsTo) => $belongsTo->withTrashed(), 'post', 'post.person']);

        if ($request->has('article')) {
            $query->where('article_id', $request->integer('article'));
        }

        if ($request->has('user')) {
            $query->withPersonDetails('user_id', $request->integer('user'));
        }

        if ($request->has('commenter')) {
            $commenter = $request->collect('commenter');

            $details = [];

            if ($name = $commenter->get('name')) {
                $details['name'] = $name;
            }

            if ($email = $commenter->get('email')) {
                $details['email'] = $email;
            }

            $query->findPersonDetails($details);
        }

        $query->afterQuery(function ($found) use ($request) {
            $show = CommentStatusEnum::tryFrom((string) $request->str('show'));

            /**
             * @see BackupController for information on why values() needs to be used.
             */

            return ! is_null($show) ? $found->status($show)->values() : null;
        });

        return new CommentCollection($query->paginate());
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        return $comment->load(['post.person'])->appendPresenter();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
            'status' => ['nullable', Rule::enum(CommentStatusEnum::class)],
        ]);

        $newComment = $request->str('comment');

        if ($request->has('title')) {
            $comment->title = $request->str('title');
        }

        if ($request->has('comment') && $comment->comment !== $newComment) {
            $comment->comment = $newComment;
        }

        $oldStatus = CommentStatusEnum::from($comment->status);

        if ($request->has('status')) {
            $status = $request->enum('status', CommentStatusEnum::class);

            $commentStatus = new CommentStatus([
                'status' => $status,
            ]);

            $commentStatus->user()->associate($request->user());
            $commentStatus->comment()->associate($comment);

            $commentStatus->save();
        }

        $comment->save();

        // Refresh comment so the status change is reflected.
        $comment->refresh();

        CommentUpdated::dispatchIf($comment->wasChanged(), $comment);
        CommentStatusChanged::dispatchIf($request->has('status'), $comment, $oldStatus);

        return $comment;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $comment->post->delete();

        CommentRemoved::dispatch($comment);

        return [
            'success' => __('Comment was removed.'),
        ];
    }
}
