<?php

namespace App\Listeners\Contact;

use App\Events\Contact\ContactSubmissionApproved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

use App\Mail\ContactedConfirmation;

class SendContactedConfirmationMessage
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
    public function handle(ContactSubmissionApproved $event): void
    {
        Mail::send(ContactedConfirmation::create($event->name, $event->email, $event->message));
    }
}
