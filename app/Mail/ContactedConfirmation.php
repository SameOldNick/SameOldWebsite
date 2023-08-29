<?php

namespace App\Mail;

use App\Components\Settings\ContactPageSettings;
use App\Http\Requests\ContactRequest;

class ContactedConfirmation extends MarkdownTemplate
{
    use Concerns\HasPlaceholders;

    protected $content;

    public function __construct(
        protected ContactRequest $request
    ) {
    }

    public function build(ContactPageSettings $settings)
    {
        $replyTo = $settings->setting('sender_replyto');
        $subject = $settings->setting('sender_subject');
        $message = $settings->setting('sender_message');

        $placeholders = $this->buildPlaceholders([
            'name' => $this->request->name,
            'email' => $this->request->email,
            'subject' => $subject,
            'message' => $this->request->message,
        ]);

        $this->content = $this->fillPlaceholders($placeholders, $message);

        $this
            ->to($this->request->email)
            ->replyTo($replyTo)
            ->subject($this->fillPlaceholders($placeholders, $subject));
    }

    protected function getContent()
    {
        return $this->content;
    }
}
