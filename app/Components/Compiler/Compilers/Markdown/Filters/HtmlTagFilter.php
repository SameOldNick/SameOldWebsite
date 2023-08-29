<?php

namespace App\Components\Compiler\Compilers\Markdown\Filters;

class HtmlTagFilter implements HtmlFilter
{
    protected $tags;

    protected $includeContents;

    public function __construct(array $tags, bool $includeContents = false)
    {
        $this->tags = $tags;
        $this->includeContents = $includeContents;
    }

    public function filter(string $html)
    {
        return preg_replace($this->getPattern(), $this->includeContents ? '$2' : '', $html);
    }

    protected function getPattern()
    {
        $quoted = implode('|', array_map('preg_quote', $this->tags));

        return "/<\s*\/?([{$quoted}])\s*[^>]*?>([^\<]*)/mi";
    }
}
