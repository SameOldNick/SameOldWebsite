<?php

namespace App\View\Components\Homepage;

use Closure;
use App\Traits\Support\HasPageSettings;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Biography extends Component
{
    use HasPageSettings;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    public function biography() {
        return $this->getPageSettings('homepage')->setting('biography');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.homepage.biography');
    }
}
