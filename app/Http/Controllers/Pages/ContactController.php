<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Components\Settings\PageSettings;

abstract class ContactController extends SettingsController
{
    protected function getPageKey() {
        return 'contact';
    }
}
