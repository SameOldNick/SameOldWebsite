<?php

namespace App\Mail;

use App\Components\Placeholders\Factory as PlaceholdersFactory;
use App\Components\Placeholders\Options;
use App\Components\Placeholders\Compilers\TagCompiler;
use App\Components\Settings\ContactPageSettings;
use App\Http\Requests\ContactRequest;

class ContactedConfirmation extends MarkdownTemplate
{
    protected $content;

    public function __construct(
        protected ContactRequest $request
    ) {
    }

    public function build(ContactPageSettings $settings, PlaceholdersFactory $factory)
    {
        $replyTo = $settings->setting('sender_replyto');
        $subject = $settings->setting('sender_subject');
        $message = $settings->setting('sender_message');

        $collection = $factory->build(function (Options $options) use ($subject) {
            $options
                ->useDefaultBuilders()
                ->set('name', $this->request->name)
                ->set('email', $this->request->email)
                ->set('subject', $subject)
                ->set('message', $this->request->message);
        });

        $tagCompiler = new TagCompiler($collection);

        $this->content = $tagCompiler->compile($message);

        $this
            ->to($this->request->email)
            ->replyTo($replyTo)
            ->subject($tagCompiler->compile($subject));
    }

    protected function getContent()
    {
        return $this->content;
    }
}
