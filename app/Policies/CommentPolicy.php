<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use App\Traits\Controllers\HasPage;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Http\Request;

class CommentPolicy
{
    use HandlesAuthorization;
    use HasPage;

    public function __construct(
        protected readonly Request $request
    ) {}

    /**
     * Determine whether the user can view any models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
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

            if (! is_null($selected)) {
                // Check if comment input ID matches Comment model ID or comment input ID is one of Comment model's children ID.
                if ($comment->is($selected)) {
                    return true;
                } elseif ($comment->allChildren()->contains(fn ($item) => $item->is($selected))) {
                    return true;
                } elseif ($selected->allChildren()->contains(fn ($item) => $item->is($comment))) {
                    return true;
                }
            }
        }

        return ! is_null($user) && $comment->post->user->is($user);
    }

    /**
     * Determine whether the user can comment on article.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(?User $user)
    {
        $userAuthentication = $this->getSettings()->setting('user_authentication', 'registered');

        return match ($userAuthentication) {
            'registered' => ! is_null($user),
            'guest_verified' => true,
            'guest_unverified' => true,
            default => false
        };
    }

    /**
     * Determine whether the user can reply to comment.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reply(?User $user, Comment $comment)
    {
        return $this->create($user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Comment $comment)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Comment $comment)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Comment $comment)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Comment $comment)
    {
        //
    }

    /**
     * Gets the key for the page.
     *
     * @return string
     */
    protected function getPageKey()
    {
        return 'blog';
    }
}
