<?php

namespace App\Components\Placeholders\Builders;

use App\Components\Placeholders\PlaceholderCollection;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Arr;

class GeneralBuilder {
    public function __construct()
    {

    }

    public function __invoke() {
        return [
            'datetime' => function(Kernel $kernel) {
                $dateTime = $kernel->requestStartedAt() ?? now();

                return $dateTime->toIso8601String();
            }
        ];
    }
}
