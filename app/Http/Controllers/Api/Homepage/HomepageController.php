<?php

namespace App\Http\Controllers\Api\Homepage;

use App\Http\Controllers\Controller;
use App\Traits\Controllers\HasPage;

abstract class HomepageController extends Controller
{
    use HasPage;

    /**
     * @inheritDoc
     */
    protected function getPageKey()
    {
        return 'homepage';
    }
}
