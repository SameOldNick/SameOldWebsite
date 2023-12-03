<?php

namespace Tests\Feature\Traits;

use App\Components\Settings\PageSettings;
use App\Models\Page;

trait ManagesPageSettings
{
    public function setUpManagesPageSettings()
    {
    }

    public function tearManagesPageSettings()
    {
    }

    public function pageSetting(string $page, $setting, $default = null)
    {
        if (is_string($setting)) {
            return $this->pageSettings($page)->setting($setting, $default);
        } elseif (is_array($setting)) {
            $page = Page::firstWhere('page', $page);

            foreach ($setting as $key => $value) {
                $page->metaData()->updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        return $this;
    }

    public function pageSettings(string $page)
    {
        return $this->getPageSettingsFor($page);
    }

    protected function getPageSettingsFor(string $page)
    {
        return $this->app->make(PageSettings::class, ['key' => $page]);
    }
}
