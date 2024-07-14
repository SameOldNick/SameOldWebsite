<?php

namespace App\Components\Macros;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EloquentBuilderMixin
{
    public function search() {
        return function (string $column, string $term, bool $caseSensitive = false) {
            /**
             * @var Builder $this
             */

            $value = '%' . $term . '%';

            if ($caseSensitive) {
                return $this->where($column, 'like', $value);
            } else {
                return $this->whereRaw("LOWER({$column}) LIKE LOWER(?)", [$value]);
            }
        };
    }
}
