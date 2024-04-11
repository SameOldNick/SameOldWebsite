<?php

namespace App\Components\OAuth;

use Illuminate\Support\Manager;

class OAuth extends Manager
{
    /**
     * Gets all of the driver names.
     *
     * @return array
     */
    public function all()
    {
        return array_unique([...array_keys($this->customCreators), ...array_keys($this->drivers)]);
    }

    /**
     * Gets all of the configured drivers.
     *
     * @return array
     */
    public function configured()
    {
        return array_filter($this->all(), fn ($driver) => $this->driver($driver)->isConfigured());
    }

    /**
     * Get the default driver name.
     *
     * @return string|null
     */
    public function getDefaultDriver()
    {
        return null;
    }
}
