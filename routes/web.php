<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

// Password reset route untuk email link
Route::get('/password/reset/{token}', function ($token) {
    return redirect('/test-reset-password.html?token=' . $token . '&email=' . request('email'));
})->name('password.reset');

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
