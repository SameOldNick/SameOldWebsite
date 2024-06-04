<?php

namespace App\Http\Requests\Parsers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class SearchQueryParser
{
    const REGEX_TAGS = '/\[([^\]]+)\]/';

    const REGEX_GROUPED = '/"([^"]+)"/';

    protected $request;

    protected $tags;

    protected $keywords;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->tags = collect();
        $this->keywords = collect();
    }

    /**
     * Parses search query
     *
     * @return static
     */
    public function parse()
    {
        $input = $this->request->str('q', '');

        $this->tags = $this->parseTags($input);
        $this->keywords = $this->parseKeywords($input);

        return $this;
    }

    /**
     * Checks if tags were specified.
     *
     * @return bool
     */
    public function hasTags()
    {
        return $this->tags->isNotEmpty();
    }

    /**
     * Gets tags in search query
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTags()
    {
        return collect($this->tags);
    }

    /**
     * Checks if keywords were specified.
     *
     * @return bool
     */
    public function hasKeywords()
    {
        return $this->keywords->isNotEmpty();
    }

    /**
     * Gets keywords in search query
     *
     * @return \Illuminate\Support\Collection
     */
    public function getKeywords()
    {
        return collect($this->keywords);
    }

    /**
     * Parses tags
     *
     * @return \Illuminate\Support\Collection
     */
    protected function parseTags(Stringable $input)
    {
        return
            $input
                ->matchAll(static::REGEX_TAGS)
                    // Uses slug instead kebab to get rid of punctuation marks
                ->map(fn ($value) => Str::slug($value))
                ->unique();
    }

    /**
     * Parses keywords
     *
     * @return \Illuminate\Support\Collection
     */
    protected function parseKeywords(Stringable $input)
    {
        $remaining = $input->replaceMatches(static::REGEX_TAGS, '')->squish();

        // Group keywords in quotes together
        $grouped = $remaining->matchAll(static::REGEX_GROUPED);

        $remaining = $remaining->replaceMatches(static::REGEX_GROUPED, '');

        // Get individual keywords
        $singles = $remaining->split('/\s+/');

        return $grouped->concat($singles)->unique()->filter();
    }
}
