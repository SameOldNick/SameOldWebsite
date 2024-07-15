<?php

namespace App\Listeners\Comments;

use App\Components\Moderator\ModerationService;
use App\Components\Settings\Facades\PageSettings;
use App\Enums\CommentStatus;
use App\Events\Comments\CommentCreated;
use App\Events\Comments\CommentStatusChanged;
use App\Models\Comment;
use App\Notifications\CommentPosted;
use Illuminate\Support\Facades\Log;
use Illuminate\Events\Dispatcher;

class ModerateComment
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected readonly ModerationService $moderationService
    )
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentCreated $event): void
    {
        $comment = $event->comment;


    }

    public function handleCommentCreated(CommentCreated $event): void
    {
        /**
         * Don't moderate if comment is awaiting verification.
         * Moderate it when it's verified instead.
         */
        if ($event->comment->status !== CommentStatus::AwaitingVerification->value) {
            $this->moderate($event->comment);
        }
    }

    public function handleCommentStatusChanged(CommentStatusChanged $event): void {
        if ($event->comment->commenter && $event->comment->commenter->isVerified()) {
            $this->moderate($event->comment);
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

    protected function moderate(Comment $comment) {
        // Run comment through moderator
        $commentModeration = PageSettings::page('blog')->setting('comment_moderation');

        $flagged = $commentModeration !== 'disabled' ? $this->moderationService->moderate($comment) : false;
        $oldStatus = $comment->status;

        if (($commentModeration === 'auto' && ! $flagged) || $commentModeration === 'disabled') {
            $comment->statuses()->create(['status' => CommentStatus::Approved]);

            // Dispatch event
            CommentStatusChanged::dispatch($comment->refresh(), CommentStatus::from($oldStatus));
        }
    }
}
