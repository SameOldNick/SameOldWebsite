<?php

namespace App\Components\Macros;

use ArrayAccess;
use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class CollectionMixin
{
    public function paginate()
    {
        return function (int $showPerPage) {
            /**
             * @var \Illuminate\Support\Collection $this
             */
            $pageNumber = Paginator::resolveCurrentPage('page');

            $totalPageNumber = $this->count();

            return $this->paginator($this->forPage($pageNumber, $showPerPage), $totalPageNumber, $showPerPage, $pageNumber, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
        };
    }

    /**
     * Create a new length-aware paginator instance.
     */
    protected static function paginator()
    {
        return function (ArrayAccess $items, int $total, int $perPage, int $currentPage, array $options) {
            return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
                'items', 'total', 'perPage', 'currentPage', 'options'
            ));
        };
    }
}
