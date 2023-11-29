<?php

namespace App\Components\Security\Responders\EventResponder;

use App\Components\Security\Issues\Issue;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Event
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Issue $issue
    ) {
        //
    }
}
