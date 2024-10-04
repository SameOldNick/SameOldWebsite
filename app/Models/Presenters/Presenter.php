<?php

namespace App\Models\Presenters;

use App\Models\Comment;
use Illuminate\Contracts\Support\Arrayable;
use Spatie\Url\Url as SpatieUrl;
use Illuminate\Support\Str;

class Presenter implements Arrayable
{
    /**
     * Specifies presenter attributes to include with model serialization.
     *
     * @return array
     */
    public function toArray()
    {
        return [];
    }
}
