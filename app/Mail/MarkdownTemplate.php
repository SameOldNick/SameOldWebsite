<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;

class MarkdownTemplate extends Mailable
{
    use Queueable;
    use Concerns\SerializesMail;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.template',
            with: [
                'content' => nl2br($this->getContent()),
            ]
        );
    }

    /**
     * Gets the content for the template.
     *
     * @return string
     */
    protected function getContent()
    {
        return '';
    }
}
