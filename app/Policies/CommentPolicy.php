<?php

namespace App\Policies;

use App\Enums\CommentStatus;
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
        return $user->can('role-manage-comments');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(?User $user, Comment $comment)
    {
        if (in_array($comment->status, [CommentStatus::Approved->value, CommentStatus::Locked->value])) {
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

        if (! is_null($user)) {
            // Give access if user is who posted comment
            if ($comment->post?->user?->is($user)) {
                return true;
            }
            // Provide access if user has manage_comments role
            elseif ($user->can('role-manage-comments')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can comment on article.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(?User $user)
    {
        if (! is_null($user) && $user->can('role-manage-comments')) {
            return true;
        }

        return $this->canComment($user);
    }

    /**
     * Determine whether the user can reply to comment.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reply(?User $user, Comment $comment)
    {
        if (! is_null($user) && $user->can('role-manage-comments')) {
            return true;
        }

        $locked = $comment->status === CommentStatus::Locked->value || $comment->allParents()->contains(fn (Comment $comment) => $comment->status === CommentStatus::Locked->value);

        return $this->canComment($user) && ! $locked;
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
     * Checks if registered or guest user can comment
     *
     * @return bool
     */
    protected function canComment(?User $user)
    {
        $userAuthentication = $this->getSettings()->setting('user_authentication', 'registered');

        return match ($userAuthentication) {
            'registered' => ! is_null($user),
            'guest_verified', 'guest_unverified' => true,
            default => false
        };
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
