<?php

namespace App\Components\Compiler\Compilers\Markdown\Filters;

use Illuminate\Support\Arr;
use DOMDocument;

class ImageFilter implements DomFilter
{
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function filter(DOMDocument $dom)
    {
        /**
         * @var \DOMElement[]
         */
        $nodes = [
            ...iterator_to_array($dom->getElementsByTagName('img'), false),
            ...iterator_to_array($dom->getElementsByTagName('picture'), false)
        ];

        foreach ($nodes as $node) {
            $srcs = [];

            if ($node->nodeName === 'img' && ($node->parentNode && $node->parentNode->nodeName !== 'picture')) {
                array_push($srcs, $node->getAttribute('src'));
            } else {
                foreach ($node->childNodes as $child) {
                    /**
                     * @var \DOMElement $child
                     */
                    if ($child->nodeName === 'source' || $child->nodeName === 'img') {
                        if ($child->hasAttribute('srcset')) {
                            array_push($srcs, $node->getAttribute('srcset'));
                        }

                        if ($child->hasAttribute('src')) {
                            array_push($srcs, $node->getAttribute('src'));
                        }
                    }

                }
            }

            foreach ($srcs as $src) {
                if ($this->isAbsoluteUrl($src)) {
                    if (! Arr::get($this->options, 'absolute_src', false)) {
                        // Remove absolute src
                        $node->remove();

                        continue;
                    }
                } else {
                    if (! Arr::get($this->options, 'relative_src', false)) {
                        // Remove relative src

                        $node->remove();

                        continue;
                    }
                }
            }
        }
    }

    protected function isAbsoluteUrl(string $url)
    {
        return (bool) preg_match('/^([a-z]+:\/\/|\/\/)/i', $url);
    }
}
