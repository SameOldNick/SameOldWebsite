<?php

namespace App\View\Components\Bootstrap;

use Illuminate\View\Component;

class Alert extends Component
{
    /**
     * The type of alert (info, success, warning, or danger)
     *
     * @var string
     */
    public $type;

    /**
     * Whether alert is dismissable
     *
     * @var bool
     */
    public $dismissable;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $type, $dismissable = false)
    {
        $this->type = $type;
        $this->dismissable = $dismissable;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.bootstrap.alert');
    }
}
