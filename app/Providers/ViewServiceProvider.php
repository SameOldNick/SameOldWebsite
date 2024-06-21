<?php

namespace App\Providers;

use App\View\Components;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\Js;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register() {}

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('datetime', function ($expression) {
            return "<?php echo ($expression)->format('m/d/Y H:i'); ?>";
        });

        ViewFacade::composer('components.main.layout', function (View $view) {
            $view->getFactory()->startPush('scripts', Arr::join($this->getScripts($view), PHP_EOL));
        });

        Blade::component('alert', Components\Bootstrap\Alert::class);
        Blade::component('alerts', Components\Bootstrap\Alerts::class);
        Blade::component('card', Components\Bootstrap\Card::class);
    }

    /**
     * Gets scripts to push to view stack
     *
     * @return array
     */
    protected function getScripts(View $view)
    {
        return [
            sprintf('<script type="text/javascript">var app = %s;</script>', Js::from($this->getInjectedVars($view))),
        ];
    }

    /**
     * Gets variables to inject into view
     *
     * @return array
     */
    protected function getInjectedVars(View $view)
    {
        return [];
    }
}
