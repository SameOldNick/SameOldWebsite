<?php

namespace App\Components\Compiler\Compilers\Markdown\Filters;

use DOMDocument;

interface DomFilter
{
    public function filter(DOMDocument $dom);
}
