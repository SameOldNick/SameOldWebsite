<?php

namespace App\Components\Settings;

use App\Models\Page;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\ForwardsCalls;

class ContactPageSettings implements Arrayable
{
    use ForwardsCalls;

    protected $page;

    protected $collection;

    public function __construct(Page $page)
    {
        $this->page = $page;
        $this->collection = $this->getPageMetaData($page);
    }

    public function page() {
        return $this->page;
    }

    public function setting($setting, $default = null) {
        $found = $this->collection->firstWhere('key', $setting);

        return !is_null($found) ? $found->value : $default;
    }

    public function settings(...$args) {
        $keys = !is_array($args[0]) ? $args : $args[0];

        return $this->collection->whereIn('key', $keys)->mapWithKeys(fn ($model) => [$model->key => $model->value]);
    }

    protected function getPageMetaData(Page $page) {
        return $page->metaData;
    }

    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray() {
        return $this->collection->mapWithKeys(fn ($model) => [$model->key => $model->value]);
    }

    public function __call($name, $arguments)
    {
        return $this->forwardDecoratedCallTo($this->collection, $name, $arguments);
    }
}
