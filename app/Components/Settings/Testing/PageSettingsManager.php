<?php

namespace App\Components\Settings\Testing;

use App\Components\Settings\Drivers\FakedDriver;
use App\Components\Settings\PageSettingsManager as BasePageSettingsManager;
use Illuminate\Contracts\Container\Container;

class PageSettingsManager extends BasePageSettingsManager
{
    public function __construct(
        Container $container,
        protected readonly array $faked
    ) {
        parent::__construct($container);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return 'faked';
    }

    /**
     * Creates faked driver
     *
     * @return FakedDriver
     */
    protected function createFakedDriver()
    {
        return new FakedDriver($this->faked);
    }
}
