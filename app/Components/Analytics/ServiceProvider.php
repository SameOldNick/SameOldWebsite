<?php

namespace App\Components\Analytics;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ChartManager::class, function ($app) {
            return new ChartManager($app);
        });

        $this->app->alias(ChartManager::class, 'charts');
    }

    /**
     * Boot any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['charts']
            ->addFromConfigFile()
            ->add('dashboard.visitors', function (AnalyticsRequest $request) {
                $period = $request->createPeriod(30);
                $dateRangeHelper = new DateRangeHelper($period, ['newUsers' => 0, 'totalUsers' => 0]);

                return new Charts\VisitorsOverTimeChart($dateRangeHelper);
            })
            ->add('dashboard.links', function (AnalyticsRequest $request) {
                $period = $request->createPeriod(30);
                $dateRangeHelper = new DateRangeHelper($period);

                return new Charts\PopularLinksChart($dateRangeHelper);
            })
            ->add('dashboard.browsers', function (AnalyticsRequest $request) {
                $period = $request->createPeriod(30);
                $dateRangeHelper = new DateRangeHelper($period);

                return new Charts\PopularBrowsersChart($dateRangeHelper);
            });
    }
}
