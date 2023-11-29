<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Http\Request;

class CommentPolicy
{
    use HandlesAuthorization;

    public function __construct(
        protected readonly Request $request
    )
    {

    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User|null  $user
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(?User $user, Comment $comment)
    {
        if ($comment->isApproved()) {
            return true;
        }

        // Provide access if previewing comment (using signed URL)
        if ($this->request->has('comment') && $this->request->hasValidSignature()) {
            $selected = Comment::find($this->request->input('comment'));

            if (!is_null($selected)) {
                // Check if comment input ID matches Comment model ID or comment input ID is one of Comment model's children ID.
                if ($comment->is($selected)) {
                    return true;
                } else if ($comment->allChildren()->contains(fn ($item) => $item->is($selected))) {
                    return true;
                } else if ($selected->allChildren()->contains(fn ($item) => $item->is($comment))) {
                    return true;
                }
            }


        }

        return ! is_null($user) && $comment->post->user->is($user);
    }

    /**
     * Determine whether the user can comment on article.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(?User $user)
    {
        return ! is_null($user);
    }

    /**
     * Determine whether the user can reply to comment.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Article  $article
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function replyTo(?User $user, Comment $comment)
    {
        return $this->create($user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Comment $comment)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Comment $comment)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Comment $comment)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Comment $comment)
    {
        //
    }
}
