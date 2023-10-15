<?php

namespace App\Components\Settings\Drivers;

use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Database\Eloquent\Collection;

class EloquentDriver {
    use ForwardsCalls;

    /**
     * Initializes Page Settings
     *
     * @param Page $page
     */
    public function __construct(
        protected Collection $collection
    ) {
    }

    /**
     * Gets setting value
     *
     * @param string $setting Key
     * @param mixed $default
     * @return mixed
     */
    public function setting($setting, $default = null)
    {
        $found = $this->collection->firstWhere('key', $setting);

        return ! is_null($found) ? $found->value : $default;
    }

    /**
     * Gets settings as array
     *
     * @param mixed ...$args Keys
     * @return array
     */
    public function settings(...$args)
    {
        $keys = ! is_array($args[0]) ? $args : $args[0];

        return $this->collection->whereIn('key', $keys)->mapWithKeys(fn ($model) => [$model->key => $model->value]);
    }

    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray()
    {
        return $this->collection->mapWithKeys(fn ($model) => [$model->key => $model->value]);
    }

    public function __call($name, $arguments)
    {
        return $this->forwardDecoratedCallTo($this->collection, $name, $arguments);
    }
}
