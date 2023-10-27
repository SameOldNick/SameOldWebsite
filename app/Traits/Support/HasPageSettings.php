<?php

namespace App\Traits\Support;

use App\Components\Settings\PageSettings;

trait HasPageSettings
{
    protected function getPageSettings(string $pageKey) {
        return app(PageSettings::class, ['key' => $pageKey]);
    }
}
