<?php

namespace App\Mail;

use App\Components\Settings\ContactPageSettings;
use App\Mail\MarkdownTemplate;
use App\Models\Page;
use App\Http\Requests\ContactRequest;
use Illuminate\Mail\Mailables\Envelope;

class Contacted extends MarkdownTemplate
{
    use Concerns\HasPlaceholders;

    protected $content;

    public function __construct(

    )
    {

    }

    public function build(ContactRequest $request, ContactPageSettings $settings) {
        $subject = $settings->setting('recipient_subject');
        $template = $settings->setting('recipient_template');

        $placeholders = $this->buildPlaceholders([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $subject,
            'message' => $request->message,
        ]);

        $this->content = $this->fillPlaceholders($placeholders, $template);

        $this
            ->to($settings->setting('recipient_email'))
            ->replyTo($request->email)
            ->subject($this->fillPlaceholders($placeholders, $subject));
    }

    protected function getContent()
    {
        return $this->content;
    }
}
