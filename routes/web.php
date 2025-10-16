<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

// Health check endpoint untuk monitoring
Route::get('/health', function () {
    $status = [
        'app' => 'ok',
        'timestamp' => now()->toISOString(),
    ];

    try {
        DB::connection()->getPdo();
        $status['database'] = 'connected';
        $status['session_driver'] = config('session.driver');
    } catch (\Exception $e) {
        $status['database'] = 'disconnected';
        $status['session_driver'] = config('session.driver');
        $status['message'] = 'Application running with fallback drivers';
    }

    return response()->json($status);
});
