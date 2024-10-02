<?php

namespace App\Components\Search\Gatherers;

use App\Components\Search\Contracts\Gatherer;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TagGatherer implements Gatherer
{
    const REGEX_TAGS = '/\[([^\]]+)\]/';

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'tags';
    }

    /**
     * {@inheritDoc}
     */
    public function gather(string $input): Collection
    {
        return
            Str::of($input)
                ->matchAll(static::REGEX_TAGS)
            // Uses slug instead kebab to get rid of punctuation marks
                ->map(fn ($value) => Str::slug($value))
                ->unique();
    }
}
