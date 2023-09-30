<?php

namespace App\Components\Placeholders;

use Illuminate\Container\Container;

class Factory
{
    public function __construct(
        protected Container $container
    ) {
    }

    /**
     * Builds placeholder collection
     *
     * @param callable $callback
     * @return PlaceholderCollection
     */
    public function build(callable $callback)
    {
        $options = new Options($this->container);

        $callback($options);

        $builders = $options->getBuilders();

        $placeholders = [];

        foreach ($builders as $builder) {
            $placeholders = [...$placeholders, ...$builder()];
        }

        return $this->createCollection($placeholders);
    }

    /**
     * Creates placeholder collection
     *
     * @param array $placeholders
     * @return PlaceholderCollection
     */
    protected function createCollection(array $placeholders)
    {
        return $this->container->makeWith(PlaceholderCollection::class, ['placeholders' => $placeholders]);
    }
}
