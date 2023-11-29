<?php

namespace App\Mail;

use App\Components\Placeholders\Compilers\TagCompiler;
use App\Components\Placeholders\Factory as PlaceholdersFactory;
use App\Components\Placeholders\Options;
use App\Http\Requests\ContactRequest;
use App\Traits\Support\HasPageSettings;

class ContactedConfirmation extends MarkdownTemplate
{
    use HasPageSettings;

    protected $content;

    protected $settings;

    public function __construct(
        protected ContactRequest $request
    ) {
        $this->settings = $this->getPageSettings('contact');
    }

    public function build(PlaceholdersFactory $factory)
    {
        $replyTo = $this->settings->setting('sender_replyto');
        $subject = $this->settings->setting('sender_subject');
        $message = $this->settings->setting('sender_message');

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
