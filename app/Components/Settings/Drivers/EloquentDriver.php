<?php

namespace App\Components\Settings\Drivers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

class EloquentDriver
{
    use ForwardsCalls;

    /**
     * Initializes the eloquent driver.
     *
     * @param Collection<int, \App\Models\PageMetaData> $collection Collection of settings for page.
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

        return $this->collection->whereIn('key', $keys)->mapWithKeys(fn ($model) => [$model->key => $model->value])->all();
    }

    /**
     * Gets the settings.
     *
     * @return array<string, mixed>
     */
    public function toArray()
    {
        return $this->collection->mapWithKeys(fn ($model) => [$model->key => $model->value])->all();
    }

    public function __call($name, $arguments)
    {
        return $this->forwardDecoratedCallTo($this->collection, $name, $arguments);
    }
}
