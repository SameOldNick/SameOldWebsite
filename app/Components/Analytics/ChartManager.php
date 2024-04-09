<?php

namespace App\Components\Analytics;

use App\Components\Analytics\Exceptions\ChartNotFoundException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;

class ChartManager
{
    protected $app;

    protected $factories;

    /**
     * Constructs Chart instance
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->factories = [];
    }

    /**
     * Adds factories from config file.
     *
     * @return $this
     */
    public function addFromConfigFile()
    {
        $this->factories = [...$this->factories, ...config('analytics.factories', [])];

        return $this;
    }

    /**
     * Adds factory for chart
     *
     * @param string $id
     * @param callable|class-string $factory Callback or name of invokable class
     * @return $this
     */
    public function add(string $id, $factory)
    {
        Arr::set($this->factories, $id, $factory);

        return $this;
    }

    /**
     * Creates factory to create chart.
     *
     * @param string $id
     * @return callable
     */
    public function createFactory(string $id)
    {
        $class = Arr::get($this->factories, $id);

        if (is_null($class)) {
            throw new ChartNotFoundException;
        }

        if (is_callable($class)) {
            return $class;
        }

        return $this->app->make($class);
    }

    /**
     * Creates the chart.
     *
     * @param string $id
     * @return Charts\Chart
     */
    public function create(string $id)
    {
        $factory = $this->createFactory($id);

        return $this->app->call($factory);
    }
}
