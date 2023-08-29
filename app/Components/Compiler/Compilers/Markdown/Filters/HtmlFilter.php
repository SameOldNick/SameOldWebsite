<?php

namespace App\Components\Compiler\Compilers\Markdown\Filters;

interface HtmlFilter
{
    public function filter(string $html);
}
