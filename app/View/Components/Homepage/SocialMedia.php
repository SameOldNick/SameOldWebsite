<?php

namespace App\View\Components\Homepage;

use Closure;
use App\Models\SocialMediaLink;
use App\Traits\Support\HasPageSettings;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SocialMedia extends Component
{
    use HasPageSettings;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    public function links() {
        return SocialMediaLink::all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.homepage.social-media');
    }
}
