<?php

namespace App\Http\Middleware;

use App\Components\SweetAlert\SweetAlertBuilder;
use App\Components\SweetAlert\SweetAlerts;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;

class ErrorsToSweetAlert
{
    public function __construct(
        protected readonly SweetAlerts $swal
    ) {
        //
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        /**
         * @var ?ViewErrorBag $errors
         */
        $errors = $request->session()->get('errors');

        if ($errors && $errors->any()) {
            $messages = $errors->unique();

            $this->swal->error(function (SweetAlertBuilder $builder) use ($messages) {
                $builder
                    ->title(__('Ooops!'))
                    ->html(count($messages) > 1 ? implode('<br>', $messages) : $messages[0]);
            });
        }

        return $next($request);
    }
}
