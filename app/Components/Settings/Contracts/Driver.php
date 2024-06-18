<?php

namespace App\Components\Settings\Contracts;

interface Driver {
    /**
     * Gets setting value
     *
     * @param string $page Page key
     * @param  string  $setting  Key
     * @param  mixed  $default
     * @return mixed
     */
    public function setting(string $page, $setting, $default = null);

    /**
     * Gets settings as array
     *
     * @param string $page Page key
     * @param  array  ...$keys  Keys to get values for
     * @return array
     */
    public function settings(string $page, ...$keys);

    /**
     * Get all settings.
     *
     * @param string $page Page key
     * @return array
     */
    public function all(string $page);
}
