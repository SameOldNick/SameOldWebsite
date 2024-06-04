<?php

namespace App\Components\SweetAlert;

class SweetAlerts
{
    protected $sweetAlerts;

    /**
     * Maintains SweetAlerts
     */
    public function __construct(array $existing)
    {
        $this->sweetAlerts = collect($existing);
    }

    /**
     * Fires sweetalert (when view is rendered)
     *
     * @param  callable  $callback  Callback that recieves instance of SweetAlertBuilder
     * @return void
     */
    public function fire(callable $callback)
    {
        $this->buildAndPushSweetAlert($this->createBuilder(), $callback);
    }

    /**
     * Fires a warning sweetalert
     *
     * @param  callable  $callback  Callback that recieves instance of SweetAlertBuilder
     * @return void
     */
    public function warning(callable $callback)
    {
        $this->buildAndPushSweetAlert($this->createBuilder()->icon('warning'), $callback);
    }

    /**
     * Fires a error sweetalert
     *
     * @param  callable  $callback  Callback that recieves instance of SweetAlertBuilder
     * @return void
     */
    public function error(callable $callback)
    {
        $this->buildAndPushSweetAlert($this->createBuilder()->icon('error'), $callback);
    }

    /**
     * Fires a success sweetalert
     *
     * @param  callable  $callback  Callback that recieves instance of SweetAlertBuilder
     * @return void
     */
    public function success(callable $callback)
    {
        $this->buildAndPushSweetAlert($this->createBuilder()->icon('success'), $callback);
    }

    /**
     * Fires a info sweetalert
     *
     * @param  callable  $callback  Callback that recieves instance of SweetAlertBuilder
     * @return void
     */
    public function info(callable $callback)
    {
        $this->buildAndPushSweetAlert($this->createBuilder()->icon('info'), $callback);
    }

    /**
     * Fires a question sweetalert
     *
     * @param  callable  $callback  Callback that recieves instance of SweetAlertBuilder
     * @return void
     */
    public function question(callable $callback)
    {
        $this->buildAndPushSweetAlert($this->createBuilder()->icon('question'), $callback);
    }

    /**
     * Creates instance of SweetAlertBuilder
     *
     * @return SweetAlertBuilder
     */
    public function createBuilder()
    {
        return new SweetAlertBuilder();
    }

    /**
     * Gets all of the sweetalerts
     *
     * @return array
     */
    public function all()
    {
        return $this->sweetAlerts->all();
    }

    /**
     * Builds SweetAlert and pushes it to collection
     *
     * @return void
     */
    protected function buildAndPushSweetAlert(SweetAlertBuilder $builder, callable $callback)
    {
        $callback($builder);

        $this->sweetAlerts->push($builder->toArray());
    }
}
