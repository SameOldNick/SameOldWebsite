<?php

namespace App\View\Components\Homepage;

use App\Components\Settings\Facades\PageSettings;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Biography extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    public function biography()
    {
        return PageSettings::page('homepage')->setting('biography');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.homepage.biography');
    }
}
