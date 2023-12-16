<?php

namespace App\Events\Articles;

use App\Models\Article;
use App\Traits\Support\HasUser;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArticleScheduled
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    use HasUser;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Article $article
    ) {
        //
    }
}
