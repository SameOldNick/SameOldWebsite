<?php

namespace Tests\Feature\Traits;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Tests\TestCase;

trait SeedsWith
{
    /**
     * Seeds with parameters
     *
     * @param class-string $class
     * @param array $params
     * @return mixed
     */
    protected function seedWith(string $class, array $params) {
        /**
         * @var TestCase $this
         */

        if (!is_subclass_of($class, Seeder::class))
            throw new InvalidArgumentException(sprintf('Class "%s" is not a subclass of "%s"', $class, Seeder::class));

        $seeder = $this->app->make($class)->setContainer($this->app);

        return $seeder->__invoke($params);
    }
}
