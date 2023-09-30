<?php

namespace App\Components\Placeholders\Builders;

use Illuminate\Support\Arr;

class ChuckNorrisBuilder
{
    public function __construct()
    {
    }

    public function __invoke()
    {
        return [
            'chuck-norris-fact' => function () {
                $facts = config('chucknorris', []);

                return Arr::random($facts);
            },
        ];
    }
}
