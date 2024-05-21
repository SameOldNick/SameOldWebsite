<?php

namespace App\Components\Compiler\Compilers\Markdown;

use App\Components\Compiler\Compilers\Compiler;
use App\Components\Compiler\Compilers\Markdown\Filters\DomFilter;
use App\Components\Compiler\Compilers\Markdown\Filters\HtmlFilter;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Masterminds\HTML5;

class MarkdownCompiler implements Compiler
{
    public function compile(string $input, array $config = [])
    {
        $filters = Arr::get($config, 'filters', []);
        $commonmark = Arr::get($config, 'commonmark', []);

        $html = ! Arr::get($config, 'inline', false) ? Str::markdown($input, $commonmark) : Str::inlineMarkdown($input, $commonmark);

        $dom = (new HTML5())->loadHTML($html);

        foreach ($filters as $filter) {
            if ($filter instanceof HtmlFilter) {
                $dom->loadHTML($filter->filter($dom->saveHTML()));
            } elseif ($filter instanceof DomFilter) {
                $filter->filter($dom);
            }
        }

        return $dom->saveHTML();
    }
}
