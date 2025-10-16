<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DatabaseHealthCheck
{
    /**
     * Handle an incoming request.
     * 
     * Middleware ini memastikan aplikasi tetap berjalan meskipun database down
     * dengan menambahkan header status database
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            DB::connection()->getPdo();
            $dbStatus = 'connected';
        } catch (\Exception $e) {
            $dbStatus = 'disconnected';
        }

        $response = $next($request);

        // Tambahkan header untuk monitoring
        if (app()->environment('local', 'development')) {
            $response->headers->set('X-Database-Status', $dbStatus);
        }

        return $response;
    }
}
