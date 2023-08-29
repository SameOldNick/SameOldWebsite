<?php

namespace App\Components\SweetAlert;

use Illuminate\View\Component;

class SweetAlertsView extends Component
{
    protected $sweetAlerts;

    /**
     * Create a new component instance.
     *
     * @param SweetAlerts $sweetAlerts Instance of SweetAlerts injected
     * @return void
     */
    public function __construct(SweetAlerts $sweetAlerts)
    {
        $this->sweetAlerts = $sweetAlerts;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.sweetalerts-view', ['sweetAlerts' => $this->sweetAlerts->all()]);
    }
}
