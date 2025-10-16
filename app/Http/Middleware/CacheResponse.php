<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $minutes = 60): Response
    {
        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // Don't cache authenticated requests (to avoid data leakage)
        if ($request->user()) {
            return $next($request);
        }

        // Generate cache key based on request
        $cacheKey = 'api_cache:' . md5($request->fullUrl());

        // Try to get from cache
        $cachedResponse = Cache::get($cacheKey);

        if ($cachedResponse) {
            return response($cachedResponse['content'], $cachedResponse['status'])
                ->header('Content-Type', $cachedResponse['content_type'])
                ->header('X-Cache-Hit', 'true');
        }

        // Process request
        $response = $next($request);

        // Cache successful responses
        if ($response->getStatusCode() === 200) {
            Cache::put($cacheKey, [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'content_type' => $response->headers->get('Content-Type'),
            ], now()->addMinutes($minutes));
        }

        $response->headers->set('X-Cache-Hit', 'false');

        return $response;
    }
}
