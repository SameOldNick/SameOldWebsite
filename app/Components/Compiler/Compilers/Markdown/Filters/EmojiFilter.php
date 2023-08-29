<?php

namespace App\Components\Compiler\Compilers\Markdown\Filters;

class EmojiFilter implements HtmlFilter
{
    public function filter(string $html)
    {
        return preg_replace('/([^-\p{L}\x00-\x7F]+)/', '', $html);
    }
}
