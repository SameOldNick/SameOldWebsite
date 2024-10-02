<?php

namespace App\Components\Search;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class ParsedQuery
{
    /**
     * Initializes the parsed query.
     *
     * @param  string  $query  The original query.
     * @param  array  $gathered  Gathered tags.
     */
    public function __construct(
        protected readonly string $query,
        protected readonly array $gathered
    ) {}

    /**
     * Gets the original search query.
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Gets the gatherer names.
     */
    public function getNames(): array
    {
        return array_keys($this->gathered);
    }

    /**
     * Checks if has any tags for gatherer.
     *
     * @param  string  $name  Name of gatherer
     */
    public function has(string $name): bool
    {
        return $this->get($name)->isNotEmpty();
    }

    /**
     * Gets tags gathered from gatherer.
     */
    public function get(string $name): Collection
    {
        if (! isset($this->gathered[$name])) {
            throw new InvalidArgumentException("Gatherer '{$name}' does not exist.");
        }

        return $this->gathered[$name];
    }
}
