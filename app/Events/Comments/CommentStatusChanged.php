<?php

namespace App\Events\Comments;

use App\Enums\CommentStatus;
use App\Models\Comment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentStatusChanged
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Comment $comment,
        public readonly CommentStatus $previous,
    ) {
        //
    }
}
