<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PegawaiMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response
    {
        // CEK LOGIN
        if(auth()->check())
        {
            return $next($request);
        }

        // JIKA BELUM LOGIN
        return redirect('/login');
    }
}