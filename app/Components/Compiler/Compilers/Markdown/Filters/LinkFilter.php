<?php

namespace App\Components\Compiler\Compilers\Markdown\Filters;

use Illuminate\Support\Arr;
use DOMDocument;

class LinkFilter implements DomFilter
{
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function filter(DOMDocument $dom)
    {
        foreach ($dom->getElementsByTagName('a') as $node) {
            /**
             * @var \DOMElement $node
             */
            $href = $node->getAttribute('href');

            if ($this->isAbsoluteUrl($href)) {
                if (! Arr::get($this->options, 'absolute_href', false)) {
                    // Remove absolute urls
                    $node->remove();

                    continue;
                }
            } else {
                if (! Arr::get($this->options, 'relative_href', false)) {
                    // Remove relative urls

                    $node->remove();

                    continue;
                }
            }

            if (Arr::get($this->options, 'nofollow', false)) {
                $node->setAttribute('rel', 'nofollow');
            }
        }
    }

    protected function isAbsoluteUrl(string $url)
    {
        return (bool) preg_match('/^([a-z]+:\/\/|\/\/)/i', $url);
    }
}
