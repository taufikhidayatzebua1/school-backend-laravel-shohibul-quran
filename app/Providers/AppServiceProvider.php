<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Graceful database connection handling
        // Otomatis fallback ke file session jika database tidak tersedia
        $this->handleDatabaseConnection();
    }

    /**
     * Handle database connection with graceful fallback
     * Best practice untuk development dan production
     */
    protected function handleDatabaseConnection(): void
    {
        try {
            // Test database connection
            DB::connection()->getPdo();
            
            // Jika berhasil, pastikan menggunakan database session
            if (config('session.driver') !== 'database') {
                config(['session.driver' => 'database']);
            }
        } catch (\Exception $e) {
            // Database tidak tersedia - fallback ke file session
            config(['session.driver' => 'file']);
            config(['cache.default' => 'file']);
            config(['queue.default' => 'sync']);
            
            // Log error hanya di production untuk monitoring
            if (app()->environment('production')) {
                Log::warning('Database connection failed, using fallback drivers', [
                    'error' => $e->getMessage(),
                    'session_driver' => 'file',
                    'cache_driver' => 'file'
                ]);
            }
        }
    }
}
