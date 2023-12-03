<?php

namespace App\Mail;

use App\Components\Placeholders\Compilers\TagCompiler;
use App\Components\Placeholders\Factory as PlaceholdersFactory;
use App\Components\Placeholders\Options;
use App\Http\Requests\ContactRequest;
use App\Mail\Concerns\BuildsMessage;
use App\Traits\Support\BuildsFromContainer;
use App\Traits\Support\HasPageSettings;

class ContactedConfirmation extends MarkdownTemplate
{
    use HasPageSettings;
    use BuildsFromContainer;

    protected $content;

    protected $settings;

    public function __construct(
        protected readonly string $name,
        protected readonly string $email,
        protected readonly string $message,
    ) {
        $this->settings = $this->getPageSettings('contact');
    }

    public function doBuild(PlaceholdersFactory $factory)
    {
        $replyTo = $this->settings->setting('sender_replyto');
        $subject = $this->settings->setting('sender_subject');
        $message = $this->settings->setting('sender_message');

        $collection = $factory->build(function (Options $options) use ($subject) {
            $options
                ->useDefaultBuilders()
                ->set('name', $this->name)
                ->set('email', $this->email)
                ->set('subject', $subject)
                ->set('message', $this->message);
        });

        $tagCompiler = new TagCompiler($collection);

        $this->content = $tagCompiler->compile($message);

        return
            $this
                ->to($this->email)
                ->replyTo($replyTo)
                ->subject($tagCompiler->compile($subject));
    }

    protected function getContent()
    {
        return $this->content;
    }
}
