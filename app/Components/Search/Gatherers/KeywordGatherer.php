<?php

namespace App\Components\Search\Gatherers;

use App\Components\Search\Contracts\Gatherer;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class KeywordGatherer implements Gatherer
{
    const REGEX_TAGS = '/\[([^\]]+)\]/';
    const REGEX_GROUPED = '/"([^"]+)"/';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'keywords';
    }

    /**
     * @inheritDoc
     */
    public function gather(string $input): Collection
    {
        $remaining = Str::of($input)->replaceMatches(static::REGEX_TAGS, '')->squish();

        // Group keywords in quotes together
        $grouped = $remaining->matchAll(static::REGEX_GROUPED);

        $remaining = $remaining->replaceMatches(static::REGEX_GROUPED, '');

        // Get individual keywords
        $singles = $remaining->split('/\s+/');

        return $grouped->concat($singles)->unique()->filter();
    }
}
