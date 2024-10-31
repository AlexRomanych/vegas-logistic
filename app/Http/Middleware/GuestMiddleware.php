<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class GuestMiddleware
{
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        if (Auth::guard($guards)->check()) {
            abort(401, 'You are not allowed to access this resource');
        }
        return $next($request);
    }
}
