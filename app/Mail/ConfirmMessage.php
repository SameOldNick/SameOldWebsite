<?php

namespace App\Mail;

use App\Components\Settings\ContactPageSettings;
use App\Http\Requests\ContactRequest;
use App\Models\PendingMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;

class ConfirmMessage extends Mailable
{
    use Queueable;
    use Concerns\HasPlaceholders;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected ContactRequest $request,
        protected PendingMessage $pendingMessage
    ) {
    }

    public function build(ContactPageSettings $settings)
    {
        $replyTo = $settings->setting('sender_replyto');
        $subject = $settings->setting('confirmation_subject');

        $placeholders = $this->buildPlaceholders([
            'name' => $this->request->name,
            'email' => $this->request->email,
            'subject' => $subject,
            'message' => $this->request->message,
        ]);

        $this
            ->to($this->request->email)
            ->replyTo($replyTo)
            ->subject($this->fillPlaceholders($placeholders, $subject));
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.confirm-message',
            with: [
                'url' => $this->pendingMessage->generateUrl(),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
