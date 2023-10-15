<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Components\Settings\PageSettings;
use App\Models\Page;
use App\Events\PageUpdated;

abstract class SettingsController extends Controller
{
    private $page;
    private $settings;

    public function __construct()
    {
        $this->page = Page::firstWhere(['page' => $this->getPageKey()]);
        $this->settings = app(PageSettings::class, ['key' => $this->getPageKey()]);
    }

    /**
     * Gets the Page model.
     *
     * @return Page
     */
    protected function getPage() {
        return $this->page;
    }

    /**
     * Dispatches PageUpdated event.
     *
     * @return $this
     */
    protected function pageUpdated() {
        PageUpdated::dispatch($this->getPage());

        return $this;
    }

    /**
     * Gets Page Settings.
     *
     * @return PageSettings
     */
    protected function getSettings() {
        return $this->settings;
    }

    /**
     * Gets the key for the page.
     *
     * @return string
     */
    abstract protected function getPageKey();
}
