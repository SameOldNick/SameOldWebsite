<?php

namespace App\Components\Compiler\Compilers\Markdown\Filters;

use Illuminate\Support\Arr;
use PHPHtmlParser\Dom;

class ImageFilter implements DomFilter {
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function filter(Dom $dom) {
        foreach ($dom->find('img, picture') as $node) {
            $srcs = [];

            if ($node->tag->name() === 'img' && $node->parent->tag->name() !== 'picture') {
                array_push($srcs, $node->getAttribute('src'));
            } else {
                foreach ($node->find('source, img') as $child) {
                    if ($child->hasAttribute('srcset'))
                        array_push($srcs, $node->getAttribute('srcset'));

                    if ($child->hasAttribute('src'))
                        array_push($srcs, $node->getAttribute('src'));
                }
            }

            foreach ($srcs as $src) {
                if ($this->isAbsoluteUrl($src)) {
                    if (!Arr::get($this->options, 'absolute_src', false)) {
                        // Remove absolute src
                        $node->delete();

                        continue;
                    }
                } else {
                    if (!Arr::get($this->options, 'relative_src', false)) {
                        // Remove relative src

                        $node->delete();

                        continue;
                    }
                }
            }

        }
    }

    protected function isAbsoluteUrl(string $url) {
        return (bool) preg_match('/^([a-z]+:\/\/|\/\/)/i', $url);
    }
}
