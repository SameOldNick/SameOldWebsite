<?php

namespace App\Components\Compiler\Compilers\Markdown\Filters;

use PHPHtmlParser\Dom;

interface DomFilter
{
    public function filter(Dom $dom);
}
