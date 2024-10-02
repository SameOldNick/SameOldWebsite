<?php

namespace App\Components\Search;

use App\Components\Search\Contracts\Gatherer;

class QueryParser
{
    /**
     * The gatherers
     *
     * @var Gatherer[]
     */
    protected readonly array $gatherers;

    /**
     * Initializes the parser
     *
     * @param  Gatherer[]  $gatherers
     */
    public function __construct(array $gatherers)
    {
        $this->gatherers = $gatherers;
    }

    /**
     * Parses search query
     */
    public function parse(string $query): ParsedQuery
    {
        $gathered = [];

        foreach ($this->gatherers as $gatherer) {
            $name = $gatherer->getName();

            $gathered[$name] = $gatherer->gather($query);
        }

        return new ParsedQuery($query, $gathered);
    }
}
