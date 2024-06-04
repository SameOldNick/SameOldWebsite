<?php

namespace App\Components\Menus\Render;

use App\Components\Menus\Contracts\SingleLevelRenderer;
use Illuminate\Support\Manager as SupportManager;

class Manager extends SupportManager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return 'navbar';
    }

    /**
     * Creates navbar driver
     *
     * @return Adapter
     */
    protected function createNavbarDriver()
    {
        return $this->createAdapter($this->container->make(Drivers\NavbarRenderer::class));
    }

    /**
     * Creates list group driver
     *
     * @return Adapter
     */
    protected function createListGroupDriver()
    {
        return $this->createAdapter($this->container->make(Drivers\ListGroupRenderer::class));
    }

    /**
     * Creates footer driver
     *
     * @return Adapter
     */
    protected function createFooterDriver()
    {
        return $this->createAdapter($this->container->make(Drivers\FooterRenderer::class));
    }

    /**
     * Create render adapter for driver
     *
     * @return Adapter
     */
    protected function createAdapter(SingleLevelRenderer $driver)
    {
        return new Adapter($driver);
    }
}
