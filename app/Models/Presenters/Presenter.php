<?php

namespace App\Models\Presenters;

use Illuminate\Contracts\Support\Arrayable;

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
