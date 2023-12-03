<?php

namespace App\Listeners\Contact;

use App\Events\Contact\ContactSubmissionRequiresApproval;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Models\PendingMessage;
use App\Mail\ConfirmMessage;
use App\Mail\Contacted;

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
