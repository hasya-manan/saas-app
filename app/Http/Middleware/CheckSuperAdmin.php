<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is logged in AND is a superadmin (role:1), let them through
    if (auth()->check() && auth()->user()->role_id === 1) {
        return $next($request);
    }

        // Otherwise, kick them back to the home page or login
        abort(403, 'You do not have access to this page.');
       
    }
}
