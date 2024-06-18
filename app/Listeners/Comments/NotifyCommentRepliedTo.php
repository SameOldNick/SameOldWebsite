<?php

namespace App\Listeners\Comments;

use App\Events\Comments\CommentApproved;
use App\Notifications\CommentPosted;
use Illuminate\Support\Facades\Notification;

class NotifyCommentRepliedTo
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentApproved $event): void
    {
        $users = [];

        $author = $event->comment->post->user;
        $parent = $event->comment->parent;

        while (! is_null($parent)) {
            $user = $parent->post->user;
            $key = $user->getKey();

            if (! isset($users[$key]) && ! $author->is($user)) {
                $users[$key] = $user;
            }

            $parent = $parent->parent;
        }

        Notification::send($users, new CommentPosted($event->comment));
    }
}
