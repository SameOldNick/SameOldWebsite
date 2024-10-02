<?php

namespace App\Components\Search\Contracts;

use Illuminate\Support\Collection;

interface Gatherer
{
    /**
     * Gets the name of the gatherer.
     * The name must be in snake_case.
     * This will be used to access the gathered.
     */
    public function getName(): string;

    /**
     * Gets the gathered tags.
     */
    public function gather(string $input): Collection;
}
