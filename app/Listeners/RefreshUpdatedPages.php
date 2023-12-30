<?php

namespace App\Listeners;

use App\Events\PageUpdated;
use App\Models\Page;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;

class RefreshUpdatedPages
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected ConsoleKernelContract $artisan
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PageUpdated $event): void
    {
        if ($this->isPageValid($event->page)) {
            // TODO: Call settings cache driver directly to handle purge.
            $this->artisan->call('cache:clear');
        }
    }

    protected function isPageValid(Page $page)
    {
        $valid = ['homepage', 'contact'];

        return in_array($page->page, $valid);
    }
}
