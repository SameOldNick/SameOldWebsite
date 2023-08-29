<?php

namespace App\Mail\Concerns;

use Illuminate\Container\Container;
use Illuminate\Support\HtmlString;

trait SerializesMail
{
    protected $unserialized = false;

    public function toArray()
    {
        $this->prepareMailableForDelivery();

        $mailer = Container::getInstance()->make('mailer');

        ['html' => $html, 'text' => $text] = $this->buildView();
        $data = $this->buildViewData();

        return [
            'addresses' => $this->getAddresses(),
            'subject' => $this->subject,
            'view' => [
                'html' => $mailer->render($html, $data),
                'text' => $mailer->render($text, $data),
            ],
        ];
    }

    public function __serialize()
    {
        return $this->toArray();
    }

    public function __unserialize(array $values)
    {
        foreach ($values['addresses'] as $type => $value) {
            foreach ($value as $entry) {
                $this->{$type}($entry['address'], $entry['name']);
            }
        }

        $this->subject($values['subject']);

        $this->html($values['view']['html']);
        $this->text(new HtmlString($values['view']['text']));

        $this->unserialized = true;
    }

    protected function getAddresses()
    {
        $addresses = [];

        foreach (['to', 'cc', 'bcc', 'replyTo'] as $type) {
            if (! empty($this->{$type})) {
                $addresses[$type] = $this->{$type};
            }
        }

        return $addresses;
    }

    protected function prepareMailableForDelivery()
    {
        // Skip preparing for deliverable if unserialized (skips calling 'build' again)
        if (! $this->unserialized) {
            parent::prepareMailableForDelivery();
        }
    }
}
