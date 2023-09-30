<?php

namespace App\Mail;

use App\Components\Placeholders\Compilers\TagCompiler;
use App\Components\Placeholders\Factory as PlaceholdersFactory;
use App\Components\Placeholders\Options;
use App\Components\Settings\ContactPageSettings;
use App\Http\Requests\ContactRequest;

class Contacted extends MarkdownTemplate
{
    protected $content;

    public function __construct()
    {
    }

    public function build(ContactRequest $request, ContactPageSettings $settings, PlaceholdersFactory $factory)
    {
        $subject = $settings->setting('recipient_subject');
        $template = $settings->setting('recipient_template');

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
            ->to($settings->setting('recipient_email'))
            ->replyTo($request->email)
            ->subject($tagCompiler->compile($subject));
    }

    protected function getContent()
    {
        return $this->content;
    }
}
