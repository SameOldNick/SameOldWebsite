<?php

namespace App\View\Components\ConnectedAccounts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProviderList extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public readonly array $providers,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.connected-accounts.provider-list');
    }
}
