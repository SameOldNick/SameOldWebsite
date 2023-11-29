<?php

namespace App\Listeners;

use App\Events\Comments\CommentApproved;
use App\Notifications\CommentPosted;

class NotifyArticleAuthorCommentPosted
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
        $author = $event->comment->article->post->user;

        $author->notify(new CommentPosted($event->comment));
    }
}
