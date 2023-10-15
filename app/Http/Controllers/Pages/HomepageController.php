<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Page;

abstract class HomepageController extends SettingsController
{
    protected function getPageKey() {
        return 'homepage';
    }
}
