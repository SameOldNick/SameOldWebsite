<?php

namespace App\Listeners\Contact;

use App\Events\Contact\ContactSubmissionRequiresApproval;
use App\Mail\ConfirmMessage;
use App\Models\PendingMessage;
use Illuminate\Support\Facades\Mail;

class SendConfirmMessage
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
    public function handle(ContactSubmissionRequiresApproval $event): void
    {
        $pendingMessage = (new PendingMessage([
            'name' => $event->name,
            'email' => $event->email,
            'message' => $event->message,
        ]))->useDefaultExpiresAt();

        $pendingMessage->save();

        Mail::send(ConfirmMessage::create($event->name, $event->email, $event->message, $pendingMessage));
    }
}
