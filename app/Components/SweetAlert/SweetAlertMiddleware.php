<?php

namespace App\Components\SweetAlert;

use ArrayObject;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\ResponseTrait;
use JsonSerializable;
use Symfony\Component\HttpFoundation\Response;

class SweetAlertMiddleware
{
    /**
     * The application implementation.
     *
     * @var Application
     */
    protected $app;

    /**
     * Create a new middleware instance.
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // If response is redirect or JSON, we need to save the alerts in the session for the next request lifecycle.
        if ($request->hasSession() && ($this->isRedirectResponse($response) || $this->isJsonResponse($response))) {
            $request->session()->flash('sweetalerts', $this->app['sweetalerts']->all());
        }

        return $response;
    }

    /**
     * Checks if response is redirection
     *
     * @return bool
     */
    protected function isRedirectResponse(Response $response)
    {
        return $response->isRedirection();
    }

    /**
     * Checks if response is JSON
     *
     * @param  mixed  $response
     * @return bool
     */
    protected function isJsonResponse($response)
    {
        // The getOriginalContent() method doesn't exist in Symfony responses
        $traits = class_uses_recursive($response);
        if (! isset($traits[ResponseTrait::class])) {
            return false;
        }

        $original = $response->getOriginalContent();

        return $original instanceof Arrayable ||
               $original instanceof Jsonable ||
               $original instanceof ArrayObject ||
               $original instanceof JsonSerializable ||
               is_array($original);
    }
}
