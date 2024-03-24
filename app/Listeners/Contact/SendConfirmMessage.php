<?php

namespace App\Listeners\Contact;

use App\Events\Contact\ContactSubmissionRequiresApproval;
use App\Mail\ConfirmMessage;
use App\Models\ContactMessage;
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
        $contactMessage = (new ContactMessage([
            'name' => $event->name,
            'email' => $event->email,
            'message' => $event->message,
        ]))->useDefaultExpiresAt();

        $contactMessage->save();

        Mail::send(ConfirmMessage::create($contactMessage));
    }
}
