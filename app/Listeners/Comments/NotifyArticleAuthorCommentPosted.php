<?php

namespace App\Listeners\Comments;

use App\Enums\CommentStatus;
use App\Events\Comments\CommentCreated;
use App\Events\Comments\CommentStatusChanged;
use App\Models\Comment;
use App\Notifications\CommentPosted;
use Illuminate\Support\Facades\Log;
use Illuminate\Events\Dispatcher;

class NotifyArticleAuthorCommentPosted
{
    /**
     * Create the event subscriber.
     */
    public function __construct()
    {
        //
    }

    public function handleCommentCreated(CommentCreated $event): void
    {
        if ($this->commentIsVisible($event->comment)) {
            $this->sendNotification($event->comment);
        }
    }

    public function handleCommentStatusChanged(CommentStatusChanged $event): void {
        if ($this->commentIsVisible($event->comment)) {
            $this->sendNotification($event->comment);
        }
    }

    protected function commentIsVisible(Comment $comment) {
        /**
         * We don't use the policy because the additional logic (based on the user) could be true.
         */
        return in_array($comment->status, [CommentStatus::Approved->value, CommentStatus::Locked->value]);
    }

    protected function sendNotification(Comment $comment) {
        $author = $comment->article->post->user;

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
