<?php

namespace App\Components\Macros\Collection;

use Illuminate\Support\Collection;

/**
 * Manages weights for collection items
 *
 * @immutable
 */
final class WeightManager
{
    /**
     * Initializes the WeightManager
     */
    public function __construct(
        protected readonly Collection $collection,
        protected readonly array $weights = []
    ) {}

    /**
     * Maps results to weight
     *
     * @param  callable  $callback  Callback that recieves item and key then returns weight for item.
     * @param  bool  $skipNoWeights  If true, items with a weight of zero or negative are skipped. (default: true)
     * @return static
     */
    public function mapToWeight(callable $callback, bool $skipNoWeights = true)
    {
        $weights = $this->weights;
        $items = $this->collection->all();

        foreach ($items as $key => $item) {
            $weight = $callback($item, $key);

            if ($skipNoWeights && ! $weight) {
                continue;
            }

            if (! isset($weights[$key])) {
                $weights[$key] = $weight;
            } else {
                $weights[$key] += $weight;
            }
        }

        return new self(
            collect($items)->reject(fn ($item, $key) => ! isset($weights[$key])),
            $weights
        );
    }

    /**
     * Gets items sorted by weights
     *
     * @param  string  $order  The order to sort (either 'asc' for ascending or 'desc' for descending). (default: 'asc')
     */
    public function sortByWeights(string $order = 'asc')
    {
        $weights = collect($this->weights);

        $weights = $order !== 'desc' ? $weights->sort() : $weights->sortDesc();

        return $weights->map(fn ($weight, $key) => $this->collection[$key]);
    }

    /**
     * Gets items and their weight
     *
     * @return Collection Array with arrays containing item value (at index 0) and weight (at index 1)
     */
    public function weights()
    {
        return $this->collection->mapWithKeys(fn ($item, $key) => [$key => [$item, $this->weights[$key] ?? false]]);
    }

    /**
     * Gets copy of collection
     *
     * @return Collection
     */
    public function getCollection()
    {
        return collect($this->collection);
    }
}
