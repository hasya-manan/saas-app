<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next) :Response
    {
    // Check if user is logged in and is a Company Admin (Role 2)
        if (auth()->check() && auth()->user()->role_id === 2) {
            return $next($request);
     }

        // If they aren't, send them back to the main dashboard or login
       abort(403, 'You do not have access to this page.');
    }
}
