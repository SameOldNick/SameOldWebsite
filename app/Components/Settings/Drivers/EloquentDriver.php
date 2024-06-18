<?php

namespace App\Components\Settings\Drivers;

use App\Components\Settings\Contracts\Driver;
use App\Models\Page;
use App\Models\PageMetaData;

class EloquentDriver implements Driver
{
    /**
     * Initializes the eloquent driver.
     */
    public function __construct()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function setting(string $page, $setting, $default = null)
    {
        $found = $this->getCollection($page)->firstWhere('key', $setting);

        return ! is_null($found) ? $found->value : $default;
    }

    /**
     * {@inheritDoc}
     */
    public function settings(string $page, ...$keys)
    {
        $keys = ! is_array($keys[0]) ? $keys : $keys[0];

        return $this->getCollection($page)->whereIn('key', $keys)->mapWithKeys(fn ($model) => [$model->key => $model->value])->all();
    }

    /**
     * Gets the settings.
     *
     * @return array<string, mixed>
     */
    public function all(string $page)
    {
        return $this->getCollection($page)->mapWithKeys(fn ($model) => [$model->key => $model->value])->all();
    }

    /**
     * Gets metadata collection for page.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, PageMetaData>
     */
    public function getCollection(string $page)
    {
        return Page::firstWhere(['page' => $page])->metaData;
    }
}
