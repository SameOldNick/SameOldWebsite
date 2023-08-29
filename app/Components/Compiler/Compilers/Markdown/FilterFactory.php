<?php

namespace App\Components\Compiler\Compilers\Markdown;

use App\Components\Compiler\Compilers\Markdown\Filters\EmojiFilter;
use App\Components\Compiler\Compilers\Markdown\Filters\HtmlTagFilter;
use App\Components\Compiler\Compilers\Markdown\Filters\LinkFilter;
use App\Components\Compiler\Compilers\Markdown\Filters\ImageFilter;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FilterFactory {
    const FILTER_DEFAULT = 'default';
    const FILTER_HEADING = 'heading';
    const FILTER_BOLD = 'bold';
    const FILTER_ITALIC = 'italic';
    const FILTER_BLOCKQUOTE = 'blockquote';
    const FILTER_LIST = 'list';
    const FILTER_CODE = 'code';
    const FILTER_HORIZONTAL_RULE = 'hr';
    const FILTER_LINK = 'link';
    const FILTER_IMAGE = 'image';

    const FILTER_TABLE = 'table';
    const FILTER_FOOTNOTE = 'footnote';
    const FILTER_HEADING_ID = 'heading-id';
    const FILTER_STRIKETHROUGH = 'strikethrough';
    const FILTER_TASKLIST = 'tasklist';
    const FILTER_EMOJI = 'emoji';
    const FILTER_HIGHLIGHT = 'highlight';
    const FILTER_SUBSCRIPT = 'subscript';
    const FILTER_SUPERSCRIPT = 'superscript';

    protected $disallowed;

    public function __construct(array $disallowed)
    {
        $this->disallowed = $disallowed;
    }

    public function createFilters() {
        $stack = [];

        foreach ($this->disallowed as $key => $value) {
            $disallowed = is_string($key) ? $key : $value;
            $options = is_string($key) ? $value : [];

            $method = 'get' . Str::studly($disallowed) . 'Filters';

            if (method_exists($this, $method)) {
                $stack = array_merge($stack, Arr::wrap($this->{$method}($options)));
            }
        }

        return $stack;
    }

    protected function getDefaultFilters(array $options) {
        return [
            new HtmlTagFilter([
                'script'
            ])
        ];
    }

    protected function getHeadingFilters(array $options) {
        $tags = [
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
        ];

        return new HtmlTagFilter($tags);
    }

    protected function getTypographyFilters(array $options) {
        $disallowed = [];

        $tags = [
            'bold' => ['b', 'strong'],
            'italic' => ['i', 'em'],
            'strikethrough' => ['s', 'strike', 'del'],
            'subscript' => ['sub'],
            'superscript' => ['sup'],
            'hr' => ['hr']
        ];

        foreach ($tags as $key => $value) {
            if (!Arr::get($options, $key, false))
                array_push($disallowed, ...$value);
        }

        return new HtmlTagFilter($disallowed);
    }

    protected function getListFilters(array $options) {
        $disallowed = [];

        $tags = ['ul', 'ol', 'dl'];

        foreach ($tags as $tag) {
            if (!Arr::get($options, $tag, false))
                array_push($disallowed, $tag);
        }

        return new HtmlTagFilter($disallowed);
    }

    protected function getBlockquoteFilters(array $options) {
        return new HtmlTagFilter(['blockquote']);
    }

    protected function getCodeFilters(array $options) {
        return new HtmlTagFilter(['code']);
    }

    protected function getLinkFilters(array $options) {
        return new LinkFilter($options);
    }

    protected function getImageFilters(array $options) {
        return new ImageFilter($options);
    }

    protected function getTableFilters(array $options) {
        return new HtmlTagFilter(['table']);
    }

    protected function getEmojiFilters(array $options) {
        return new EmojiFilter;
    }
}
