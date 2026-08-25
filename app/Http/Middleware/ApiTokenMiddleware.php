<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiTokenMiddleware
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
        $token = $request->header('Authorization');
        $appToken = env('APP_API_TOKEN');
        
        if (!$token || $token !== $appToken) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access',
                'data' => null
            ], 401);
        }
        
        return $next($request);
    }
}
