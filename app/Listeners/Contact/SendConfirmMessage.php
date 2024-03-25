<?php

namespace App\Listeners\Contact;

use App\Events\Contact\ContactSubmissionRequiresApproval;
use App\Mail\ConfirmMessage;
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
        Mail::send(ConfirmMessage::create($event->message));
    }
}
