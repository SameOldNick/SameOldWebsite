<?php

namespace App\Components\Macros;

use App\Components\Macros\Collection\WeightManager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder as Schema;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Str;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Arr::mixin(new ArrMixin);
        Str::mixin(new StrMixin);
        Collection::mixin(new PaginationMixin);
        Collection::macro('weighted', function () {
            /**
             * @var Collection $this
             */
            return new WeightManager($this);
        });
        Response::mixin(new ResponseMixin);
        Blueprint::mixin(new BlueprintMixin);
        Schema::mixin(new SchemaMixin);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {}
}
