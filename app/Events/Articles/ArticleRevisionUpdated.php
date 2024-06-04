<?php

namespace App\Events\Articles;

use App\Models\Article;
use App\Traits\Support\HasUser;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArticleRevisionUpdated
{
    use Dispatchable;
    use HasUser;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Article $article
    ) {
        //
    }
}
