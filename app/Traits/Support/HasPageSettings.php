<?php

namespace App\Traits\Support;

use App\Components\Settings\PageSettings;

trait HasPageSettings
{
    /**
     * Gets page settings for page.
     *
     * @param string $pageKey
     * @return PageSettings
     */
    protected function getPageSettings(string $pageKey)
    {
        return app(PageSettings::class, ['key' => $pageKey]);
    }
}
