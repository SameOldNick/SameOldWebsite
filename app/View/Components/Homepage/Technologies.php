<?php

namespace App\View\Components\Homepage;

use Closure;

use App\Models\Technology;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Technologies extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    public function technologies() {
        return Technology::all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.homepage.technologies');
    }
}
