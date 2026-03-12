<?php

namespace App\Http\Middleware;

use App\Components\SweetAlert\SweetAlertBuilder;
use App\Components\SweetAlert\SweetAlerts;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
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
