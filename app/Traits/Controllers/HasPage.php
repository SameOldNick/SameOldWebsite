<?php

namespace App\Traits\Controllers;

use App\Components\Settings\Facades\PageSettings;
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
     */
    protected function getSettings()
    {
        return PageSettings::page($this->getPageKey());
    }

    /**
     * Gets the key for the page.
     *
     * @return string
     */
    protected function getPageKey()
    {
        return class_basename($this);
    }
}
