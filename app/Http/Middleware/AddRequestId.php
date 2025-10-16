<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AddRequestId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Generate or use existing request ID
        $requestId = $request->header('X-Request-ID') ?? (string) Str::uuid();
        
        // Add to request
        $request->headers->set('X-Request-ID', $requestId);
        
        // Share with app for logging
        app()->instance('request-id', $requestId);
        
        // Process request
        $response = $next($request);
        
        // Add to response
        $response->headers->set('X-Request-ID', $requestId);
        
        return $response;
    }
}
