<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Public\PublicHafalanController;
use App\Http\Controllers\Api\Public\PublicKelasController;
use App\Http\Controllers\Api\Public\PublicSiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\HafalanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\OrangTuaController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| All routes are prefixed with /api/v1
|
*/

Route::prefix(config('api.version', 'v1'))->group(function () {
    
    // Auth routes (no authentication required) - Rate limited to prevent brute force
    Route::prefix('auth')->middleware('throttle:' . config('api.rate_limit.auth', 10) . ',1')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    // Public routes (no authentication required) - Rate limited to prevent abuse
    // Uses Public Controllers with limited data exposure
    // Cached for 30 minutes to improve performance
    Route::prefix('public')->middleware([
        'throttle:' . config('api.rate_limit.public', 60) . ',1', 
        'cache.response:' . config('api.cache.public_endpoints', 30)
    ])->group(function () {
        // Hafalan routes (no authentication required)
        Route::prefix('hafalan')->group(function () {
            Route::get('/', [PublicHafalanController::class, 'index']);
            Route::get('/{id}', [PublicHafalanController::class, 'show']);
        });

        // Kelas routes (no authentication required)
        Route::prefix('kelas')->group(function () {
            Route::get('/', [PublicKelasController::class, 'index']);
            Route::get('/{id}', [PublicKelasController::class, 'show']);
            Route::get('/{id}/siswa', [PublicKelasController::class, 'getSiswa']);
        });

        // Siswa routes (no authentication required)
        Route::prefix('siswa')->group(function () {
            Route::get('/', [PublicSiswaController::class, 'index']);
            Route::get('/{id}', [PublicSiswaController::class, 'show']);
        });
    });

    // Protected routes (authentication required) - Higher rate limit for authenticated users
    Route::middleware(['auth:sanctum', 'throttle:' . config('api.rate_limit.protected', 200) . ',1'])->group(function () {
        // Auth routes
        Route::prefix('auth')->group(function () {
            Route::post('/register', [AuthController::class, 'register']); // Dipindah ke protected, butuh auth
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/profile', [AuthController::class, 'profile']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::post('/revoke-tokens', [AuthController::class, 'revokeAllTokens']);
        });

        // Legacy route for backward compatibility
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // User Management routes (hanya untuk tata-usaha, admin, super-admin)
        Route::prefix('users')->middleware('role:tata-usaha,admin,super-admin')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::post('/', [UserController::class, 'store']);
            Route::get('/available-roles', [UserController::class, 'availableRoles']);
            Route::get('/{user}', [UserController::class, 'show']);
            Route::put('/{user}', [UserController::class, 'update']);
            Route::delete('/{user}', [UserController::class, 'destroy']);
        });

        // Protected routes with role check (guru, kepala-sekolah, admin, super-admin)
        Route::middleware('role:guru,kepala-sekolah,admin,super-admin')->group(function () {
            // Tahun Ajaran routes (with authentication and role check)
            Route::prefix('tahun-ajaran')->group(function () {
                Route::get('/', [TahunAjaranController::class, 'index']);
                Route::get('/active', [TahunAjaranController::class, 'active']);
                Route::post('/', [TahunAjaranController::class, 'store']);
                Route::get('/{id}', [TahunAjaranController::class, 'show']);
                Route::put('/{id}', [TahunAjaranController::class, 'update']);
                Route::post('/{id}/set-active', [TahunAjaranController::class, 'setActive']);
                Route::delete('/{id}', [TahunAjaranController::class, 'destroy']);
            });

            // Hafalan routes (with authentication and role check)
            Route::prefix('hafalan')->group(function () {
                Route::get('/', [HafalanController::class, 'index']);
                Route::post('/', [HafalanController::class, 'store']);
                Route::get('/statistics', [HafalanController::class, 'statistics']);
                Route::get('/{id}', [HafalanController::class, 'show']);
                Route::put('/{id}', [HafalanController::class, 'update']);
                Route::delete('/{id}', [HafalanController::class, 'destroy']);
            });

            // Kelas routes (with authentication and role check)
            Route::prefix('kelas')->group(function () {
                Route::get('/', [KelasController::class, 'index']);
                Route::get('/{id}', [KelasController::class, 'show']);
                Route::get('/{id}/siswa', [KelasController::class, 'getSiswa']);
            });

            // Siswa routes (with authentication and role check)
            Route::prefix('siswa')->group(function () {
                // Read access for all authenticated users with base roles
                Route::get('/', [SiswaController::class, 'index']);
                Route::get('/{id}', [SiswaController::class, 'show']);
                Route::get('/{id}/hafalan', [SiswaController::class, 'getHafalan']);
                Route::get('/{id}/statistics', [SiswaController::class, 'getStatistics']);
                
                // Write access only for tata-usaha, admin, super-admin
                Route::middleware('role:tata-usaha,admin,super-admin')->group(function () {
                    Route::post('/', [SiswaController::class, 'store']);
                    Route::put('/{id}', [SiswaController::class, 'update']);
                    Route::delete('/{id}', [SiswaController::class, 'destroy']);
                });
            });

            // Guru routes (with authentication and role check)
            Route::prefix('guru')->group(function () {
                // Read access for all authenticated users with base roles
                Route::get('/', [GuruController::class, 'index']);
                Route::get('/{id}', [GuruController::class, 'show']);
                
                // Write access only for tata-usaha, admin, super-admin
                Route::middleware('role:tata-usaha,admin,super-admin')->group(function () {
                    Route::post('/', [GuruController::class, 'store']);
                    Route::put('/{id}', [GuruController::class, 'update']);
                    Route::delete('/{id}', [GuruController::class, 'destroy']);
                });
            });

            // Orang Tua routes (with authentication and role check)
            Route::prefix('orang-tua')->group(function () {
                // Read access for all authenticated users with base roles
                Route::get('/', [OrangTuaController::class, 'index']);
                Route::get('/{id}', [OrangTuaController::class, 'show']);
                
                // Write access only for tata-usaha, admin, super-admin
                Route::middleware('role:tata-usaha,admin,super-admin')->group(function () {
                    Route::post('/', [OrangTuaController::class, 'store']);
                    Route::put('/{id}', [OrangTuaController::class, 'update']);
                    Route::delete('/{id}', [OrangTuaController::class, 'destroy']);
                });
            });
        });
    });

}); // End of API v1

/*
|--------------------------------------------------------------------------
| API Health Check
|--------------------------------------------------------------------------
*/
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'version' => 'v1',
        'timestamp' => now()->toIso8601String(),
    ]);
});

