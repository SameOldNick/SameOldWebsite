<?php

namespace App\Mail;

use App\Components\Placeholders\Compilers\TagCompiler;
use App\Components\Placeholders\Factory as PlaceholdersFactory;
use App\Components\Placeholders\Options;
use App\Traits\Support\HasPageSettings;
use App\Http\Requests\ContactRequest;

class Contacted extends MarkdownTemplate
{
    use HasPageSettings;

    protected $content;
    protected $settings;

    public function __construct()
    {
        $this->settings = $this->getPageSettings('contact');
    }

    public function build(ContactRequest $request, PlaceholdersFactory $factory)
    {
        $subject = $this->settings->setting('recipient_subject');
        $template = $this->settings->setting('recipient_template');

        $collection = $factory->build(function (Options $options) use ($request, $subject) {
            $options
                ->useDefaultBuilders()
                ->set('name', $request->name)
                ->set('email', $request->email)
                ->set('subject', $subject)
                ->set('message', $request->message);
        });

        $tagCompiler = new TagCompiler($collection);

        $this->content = $tagCompiler->compile($template);

        $this
            ->to($this->settings->setting('recipient_email'))
            ->replyTo($request->email)
            ->subject($tagCompiler->compile($subject));
    }

    protected function getContent()
    {
        return $this->content;
    }
}
