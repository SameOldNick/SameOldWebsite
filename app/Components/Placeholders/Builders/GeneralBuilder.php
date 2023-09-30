<?php

namespace App\Components\Placeholders\Builders;

use Illuminate\Contracts\Http\Kernel;

class GeneralBuilder
{
    public function __construct()
    {
    }

    public function __invoke()
    {
        return [
            'datetime' => function (Kernel $kernel) {
                $dateTime = $kernel->requestStartedAt() ?? now();

                return $dateTime->toIso8601String();
            },
        ];
    }
}
