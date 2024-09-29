<?php

namespace App\Traits\Controllers;

use Spatie\GoogleTagManager\GoogleTagManager;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

trait Trackable
{
    /**
     * Gets the tracker instance.
     *
     * @return GoogleTagManager
     */
    protected function tracker(): GoogleTagManager {
        return GoogleTagManagerFacade::getFacadeRoot();
    }
}
