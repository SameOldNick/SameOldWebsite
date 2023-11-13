<?php

namespace App\Components\Analytics;

use App\Components\Analytics\Exceptions\ChartNotFoundException;
use App\Components\Charts\Factories\Factory;
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

    public function addFromConfigFile()
    {
        $this->factories = [...$this->factories, ...config('analytics.factories', [])];

        return $this;
    }

    /**
     * Adds factory for chart
     *
     * @param string $id
     * @param Factory|string $factory Factory instance or name of Factory class
     * @return $this
     */
    public function add(string $id, $factory)
    {
        Arr::set($this->factories, $id, $factory);

        return $this;
    }

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

    public function create(string $id)
    {
        $factory = $this->createFactory($id);

        return $this->app->call($factory);
    }
}
