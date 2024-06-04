<?php

namespace App\Mail;

use App\Components\Placeholders\Compilers\TagCompiler;
use App\Components\Placeholders\Factory as PlaceholdersFactory;
use App\Components\Placeholders\Options;
use App\Models\ContactMessage;
use App\Traits\Support\BuildsFromContainer;
use App\Traits\Support\HasPageSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;

class ConfirmMessage extends Mailable
{
    use BuildsFromContainer;
    use HasPageSettings;
    use Queueable;

    protected $settings;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected readonly ContactMessage $contactMessage
    ) {
        $this->settings = $this->getPageSettings('contact');
    }

    public function doBuild(PlaceholdersFactory $factory)
    {
        $replyTo = $this->settings->setting('sender_replyto');
        $subject = $this->settings->setting('confirmation_subject');

        $collection = $factory->build(function (Options $options) use ($subject) {
            $options
                ->useDefaultBuilders()
                ->set('name', $this->contactMessage->name)
                ->set('email', $this->contactMessage->email)
                ->set('subject', $subject)
                ->set('message', $this->contactMessage->message);
        });

        $tagCompiler = new TagCompiler($collection);

        return
            $this
                ->to($this->contactMessage->email)
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
                'url' => $this->contactMessage->generateUrl(),
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
