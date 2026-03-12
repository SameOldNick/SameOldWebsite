<?php

namespace App\Http\Middleware;

use App\Models\File;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Middleware\ValidateSignature;

class FileAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (is_null($request->file)) {
            throw (new ModelNotFoundException)->setModel(File::class);
        }

        return $request->file->is_public ? $next($request) : (new ValidateSignature)->handle($request, $next);
    }
}
