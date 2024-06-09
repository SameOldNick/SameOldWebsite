<?php

namespace App\Traits\Controllers;

use App\Components\Settings\PageSettings;
use App\Events\PageUpdated;
use App\Models\Page;

trait HasPage
{
    /**
     * Gets the Page model.
     *
     * @return Page
     */
    protected function getPage()
    {
        return Page::firstWhere(['page' => $this->getPageKey()]);
    }

    /**
     * Dispatches PageUpdated event.
     *
     * @return $this
     */
    protected function pageUpdated()
    {
        PageUpdated::dispatch($this->getPage());

        return $this;
    }

    /**
     * Gets Page Settings.
     *
     * @return PageSettings
     */
    protected function getSettings()
    {
        return app(PageSettings::class, ['key' => $this->getPageKey()]);
    }

    /**
     * Gets the key for the page.
     *
     * @return string
     */
    abstract protected function getPageKey();
}
