<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

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
                'content' => $this->getContent(),
            ]
        );
    }

    /**
     * Gets the content for the template.
     *
     * @return string
     */
    protected function getContent() {
        return '';
    }
}
