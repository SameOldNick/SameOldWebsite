<?php

namespace App\Components\Analytics\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class GoogleAnalyticsNotConfiguredException extends HttpException
{
    public function __construct()
    {
        parent::__construct(501, __('The Google Analytics client is not configured correctly.'));
    }
}
