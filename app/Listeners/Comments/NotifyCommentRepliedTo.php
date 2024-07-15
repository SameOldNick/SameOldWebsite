<?php

namespace App\Listeners\Comments;

use App\Enums\CommentStatus;
use App\Events\Comments\CommentCreated;
use App\Events\Comments\CommentStatusChanged;
use App\Models\Comment;
use App\Notifications\CommentPosted;
use Illuminate\Support\Facades\Notification;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

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
     * Handle the event.
     */
    public function handle(CommentCreated $event): void
    {

    }

    /**
     * Handle the event.
     */
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

    protected function commentIsVisible(Comment $comment) {
        /**
         * We don't use the policy because the additional logic (based on the user) could be true.
         */
        return in_array($comment->status, [CommentStatus::Approved->value, CommentStatus::Locked->value]);
    }

    protected function sendNotification(Comment $comment) {
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
            if ($notifiable = $parent->commenter ?? $parent->post->user) {
                $notifiables->push($notifiable);
            }

            $parent = $parent->parent;
        }

        // Return unique notifiables using Laravel's unique method
        return $notifiables->unique();
    }
}
