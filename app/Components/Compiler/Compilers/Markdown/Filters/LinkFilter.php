<?php

namespace App\Components\Compiler\Compilers\Markdown\Filters;

use Illuminate\Support\Arr;
use PHPHtmlParser\Dom;

class LinkFilter implements DomFilter {
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function filter(Dom $dom) {
        foreach ($dom->find('a') as $node) {
            $href = $node->getAttribute('href');

            if ($this->isAbsoluteUrl($href)) {
                if (!Arr::get($this->options, 'absolute_href', false)) {
                    // Remove absolute urls
                    $node->delete();

                    continue;
                }
            } else {
                if (!Arr::get($this->options, 'relative_href', false)) {
                    // Remove relative urls

                    $node->delete();

                    continue;
                }
            }

            if (Arr::get($this->options, 'nofollow', false)) {
                $node->setAttribute('rel', 'nofollow');
            }
        }
    }

    protected function isAbsoluteUrl(string $url) {
        return (bool) preg_match('/^([a-z]+:\/\/|\/\/)/i', $url);
    }
}
