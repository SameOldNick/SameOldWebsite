<?php

namespace App\View\Components\Homepage;

use App\Models\Skill;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Skills extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    public function skills()
    {
        return Skill::all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.homepage.skills');
    }
}
