<?php

namespace App\Listeners\Comments;

use App\Events\Comments\CommentApproved;
use App\Notifications\CommentPosted;
use Illuminate\Support\Facades\Log;

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

        /**
         * The article should have a user.
         * This check is for when the article wasn't created correctly in testing.
         */
        if (is_null($author)) {
            Log::error('Unable to determine article author to send notification', ['article' => $event->comment->article]);
            return;
        }

        $author->notify(new CommentPosted($event->comment));
    }
}
