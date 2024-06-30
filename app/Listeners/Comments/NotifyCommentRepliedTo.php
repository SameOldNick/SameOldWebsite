<?php

namespace App\Listeners\Comments;

use App\Events\Comments\CommentApproved;
use App\Models\Comment;
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
        $notifiables = $this->getNotifiables($event->comment);

        Notification::send($notifiables, new CommentPosted($event->comment));
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
