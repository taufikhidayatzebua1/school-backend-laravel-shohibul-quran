<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'cache.response' => \App\Http\Middleware\CacheResponse::class,
        ]);
        
        // Add security headers and request ID to all responses
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->append(\App\Http\Middleware\AddRequestId::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle Authentication Exception (401)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }
        });
        
        // Handle Not Found Exception (404)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint not found.',
                    'path' => $request->path()
                ], 404);
            }
        });
        
        // Handle Method Not Allowed Exception (405)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method not allowed.',
                    'allowed_methods' => $e->getHeaders()['Allow'] ?? 'N/A'
                ], 405);
            }
        });
        
        // Handle Validation Exception (422)
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
        });
        
        // Handle General Exception (500)
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                // Only show detailed error in development
                $message = config('app.debug') 
                    ? $e->getMessage() 
                    : 'Internal server error.';
                
                $response = [
                    'success' => false,
                    'message' => $message
                ];
                
                // Add debug info in development
                if (config('app.debug')) {
                    $response['debug'] = [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => collect($e->getTrace())->take(5)->toArray()
                    ];
                }
                
                return response()->json($response, 500);
            }
        });
    })->create();
