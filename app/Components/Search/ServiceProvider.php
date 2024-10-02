<?php

namespace App\Components\Search;

use App\Components\Search\Gatherers\KeywordGatherer;
use App\Components\Search\Gatherers\TagGatherer;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(QueryParser::class, function ($app) {
            $gatherers = [
                $app->make(KeywordGatherer::class),
                $app->make(TagGatherer::class),
            ];

            return new QueryParser($gatherers);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {}
}
