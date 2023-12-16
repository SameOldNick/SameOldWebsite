<?php

namespace App\Components\Analytics\Charts;

abstract class Chart
{
    /**
     * Generates the data used to render the chart.
     *
     * @return mixed
     */
    abstract public function generate();
}
