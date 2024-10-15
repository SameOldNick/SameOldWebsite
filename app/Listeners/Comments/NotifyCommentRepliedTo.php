<?php

namespace App\Listeners\Comments;

use App\Enums\CommentStatus;
use App\Events\Comments\CommentCreated;
use App\Events\Comments\CommentStatusChanged;
use App\Models\Comment;
use App\Notifications\CommentPosted;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Notification;

class NotifyCommentRepliedTo
{
    /**
     * Create the event subscriber.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle CommentCreated event
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
     * Handle CommentStatusChanged event
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
        Notification::send($this->getNotifiables($comment), new CommentPosted($comment));
    }

    /**
     * Get notifiables from comment
     *
     * @return \Illuminate\Support\Collection<int, mixed>
     */
    protected function getNotifiables(Comment $comment)
    {
        $notifiables = collect();

        $parent = $comment->parent;

        while ($parent) {
            if ($notifiable = $parent->post->person) {
                $notifiables->push($notifiable);
            }

            $parent = $parent->parent;
        }

        // Return unique notifiables using Laravel's unique method
        return $notifiables->unique();
    }
}
