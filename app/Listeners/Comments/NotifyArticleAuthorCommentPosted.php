<?php

namespace App\Listeners\Comments;

use App\Enums\CommentStatus;
use App\Events\Comments\CommentCreated;
use App\Events\Comments\CommentStatusChanged;
use App\Models\Comment;
use App\Notifications\CommentPosted;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

class NotifyArticleAuthorCommentPosted
{
    /**
     * Create the event subscriber.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handles CommentCreated event
     *
     * @param CommentCreated $event
     * @return void
     */
    public function handleCommentCreated(CommentCreated $event): void
    {
        if ($this->commentIsVisible($event->comment)) {
            $this->sendNotification($event->comment);
        }
    }

    /**
     * Handles CommentStatusChanged event
     *
     * @param CommentStatusChanged $event
     * @return void
     */
    public function handleCommentStatusChanged(CommentStatusChanged $event): void
    {
        if ($this->commentIsVisible($event->comment)) {
            $this->sendNotification($event->comment);
        }
    }

    /**
     * Checks if comment is visible
     *
     * @param Comment $comment
     * @return boolean
     */
    protected function commentIsVisible(Comment $comment)
    {
        /**
         * We don't use the policy because the additional logic (based on the user) could be true.
         */
        return in_array($comment->status, [CommentStatus::Approved->value, CommentStatus::Locked->value]);
    }

    /**
     * Sends notification
     *
     * @param Comment $comment
     * @return void
     */
    protected function sendNotification(Comment $comment)
    {
        $author = $comment->article->post->person;

        /**
         * The article should have a user.
         * This check is for when the article wasn't created correctly in testing.
         */
        if (is_null($author)) {
            Log::error('Unable to determine article author to send notification', ['article' => $comment->article]);

            return;
        }

        $author->notify(new CommentPosted($comment));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            CommentCreated::class => 'handleCommentCreated',
            CommentStatusChanged::class => 'handleCommentStatusChanged',
        ];
    }
}
