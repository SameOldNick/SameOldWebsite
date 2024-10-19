<?php

namespace App\Listeners;

use App\Events\PageUpdated;
use App\Components\Settings\Facades\PageSettings;

class RefreshUpdatedPages
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PageUpdated $event): void
    {
        // The cache is only used in production
        if (app()->isProduction()) {
            PageSettings::driver('cache')->purge($event->page->page);
        }
    }
}
