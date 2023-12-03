<?php

namespace App\Mail;

use App\Components\Placeholders\Compilers\TagCompiler;
use App\Components\Placeholders\Factory as PlaceholdersFactory;
use App\Components\Placeholders\Options;
use App\Http\Requests\ContactRequest;
use App\Mail\Concerns\BuildsMessage;
use App\Traits\Support\BuildsFromContainer;
use App\Traits\Support\HasPageSettings;

class Contacted extends MarkdownTemplate
{
    use HasPageSettings;
    use BuildsFromContainer;

    protected $content;

    protected $settings;

    public function __construct(
        protected readonly string $name,
        protected readonly string $email,
        protected readonly string $message,
    )
    {
        $this->settings = $this->getPageSettings('contact');
    }

    public function doBuild(PlaceholdersFactory $factory)
    {
        $subject = $this->settings->setting('recipient_subject');
        $template = $this->settings->setting('recipient_template');

        $collection = $factory->build(function (Options $options) use ($subject) {
            $options
                ->useDefaultBuilders()
                ->set('name', $this->name)
                ->set('email', $this->email)
                ->set('subject', $subject)
                ->set('message', $this->message);
        });

        $tagCompiler = new TagCompiler($collection);

        $this->content = $tagCompiler->compile($template);

        return
            $this
                ->to($this->settings->setting('recipient_email'))
                ->replyTo($this->email)
                ->subject($tagCompiler->compile($subject));
    }

    protected function getContent()
    {
        return $this->content;
    }
}
