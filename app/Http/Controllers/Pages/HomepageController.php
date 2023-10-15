<?php

namespace App\Http\Controllers\Pages;

abstract class HomepageController extends SettingsController
{
    protected function getPageKey()
    {
        return 'homepage';
    }
}
