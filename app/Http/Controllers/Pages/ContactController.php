<?php

namespace App\Http\Controllers\Pages;

abstract class ContactController extends SettingsController
{
    protected function getPageKey()
    {
        return 'contact';
    }
}
