<?php

namespace App\Traits\Support;

trait HasWeights
{
    protected $weights = [];

    /**
     * Maps results to weight
     *
     * @param callable $callback Callback that recieves item and key then returns weight for item.
     * @param bool $skipNoWeights If true, items with a weight of zero or negative are skipped. (default: true)
     * @return static
     */
    public function mapToWeight(callable $callback, bool $skipNoWeights = true)
    {
        $weights = $this->weights;
        $items = $this->all();

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

        $collection =
            (new static($items))->reject(fn ($item, $key) => ! isset($weights[$key]));

        $collection->weights = $weights;

        return $collection;
    }

    /**
     * Gets items sorted by weights
     * @param string $order The order to sort (either 'asc' for ascending or 'desc' for descending). (default: 'asc')
     *
     * @return Collection
     */
    public function sortByWeights(string $order = 'asc')
    {
        $weights = collect($this->weights);

        $weights = $order !== 'desc' ? $weights->sort() : $weights->sortDesc();

        return $weights->map(fn ($weight, $key) => $this->items[$key]);
    }

    /**
     * Gets items and their weight
     *
     * @return Collection Array with arrays containing item value (at index 0) and weight (at index 1)
     */
    public function weights()
    {
        return $this->mapWithKeys(fn ($item, $key) => [$key => [$item, $this->weights[$key] ?? false]]);
    }
}
