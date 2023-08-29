<?php

namespace App\Http\Middleware;

use App\Models\Article;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ValidateSignature;

class ArticleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (is_null($request->article)) {
            throw (new ModelNotFoundException)->setModel(Article::class);
        }

        return ! is_null($request->article->published_at) ? $next($request) : (new ValidateSignature)->handle($request, $next);
    }
}
