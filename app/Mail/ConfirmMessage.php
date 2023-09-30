<?php

namespace App\Mail;

use App\Components\Placeholders\Compilers\TagCompiler;
use App\Components\Placeholders\Factory as PlaceholdersFactory;
use App\Components\Placeholders\Options;
use App\Components\Settings\ContactPageSettings;
use App\Http\Requests\ContactRequest;
use App\Models\PendingMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;

class ConfirmMessage extends Mailable
{
    use Queueable;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected ContactRequest $request,
        protected PendingMessage $pendingMessage
    ) {
    }

    public function build(ContactPageSettings $settings, PlaceholdersFactory $factory)
    {
        $replyTo = $settings->setting('sender_replyto');
        $subject = $settings->setting('confirmation_subject');

        $collection = $factory->build(function (Options $options) use ($subject) {
            $options
                ->useDefaultBuilders()
                ->set('name', $this->request->name)
                ->set('email', $this->request->email)
                ->set('subject', $subject)
                ->set('message', $this->request->message);
        });

        $tagCompiler = new TagCompiler($collection);

        $this
            ->to($this->request->email)
            ->replyTo($replyTo)
            ->subject($tagCompiler->compile($subject));
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
