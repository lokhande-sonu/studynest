<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthManagement
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
        // Check if the user is authenticated with the management guard
        if (auth()->guard('management')->check()) {
            return $next($request);
        }
        
        // For backward compatibility, also check session
        if ($request->session()->has('uid')) {
            return $next($request);
        }
        
        // Not authenticated, redirect to login
        return redirect('management/login')->with('error', 'Please login to access this area');
    }
}
