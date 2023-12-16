<?php

namespace App\Listeners\RecentActivity;

use App\Enums\Notifications\ActivityEvent;
use App\Events\Comments\CommentCreated;
use App\Notifications\Activity;

class LogCommentCreated extends LogActivity
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
    public function handle(CommentCreated $event): void
    {
        $comment = $event->comment;
        $user = $comment->post->user;
        $article = $comment->article;

        $message = __('Comment was posted by ":user" on article ":article".', ['user' => $user->getDisplayName(), 'article' => $article->title]);
        $context = [
            'comment' => $comment,
            'article' => $article,
            'user' => $user,
        ];

        $this->log(new Activity(ActivityEvent::CommentCreated, $comment->post->created_at ?? now(), $message, $context));
    }
}
