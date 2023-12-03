<?php

namespace App\Events\Contact;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactSubmissionRequiresApproval
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $message
    ) {
        //
    }
}
