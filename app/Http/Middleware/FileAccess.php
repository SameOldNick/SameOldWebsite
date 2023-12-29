<?php

namespace App\Http\Middleware;

use App\Models\File;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ValidateSignature;

class FileAccess
{
    /**
     * Handle an incoming request.
     *
     * @param Request  $request
     * @param  Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (is_null($request->file)) {
            throw (new ModelNotFoundException)->setModel(File::class);
        }

        return $request->file->is_public ? $next($request) : (new ValidateSignature)->handle($request, $next);
    }
}
