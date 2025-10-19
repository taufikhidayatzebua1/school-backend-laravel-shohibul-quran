#!/usr/bin/env php
<?php

/**
 * Interactive Testing Menu
 * Run with: php tinker_menu.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Laravel Facade type hints for IDE
use Illuminate\Support\Facades\Cache;

function showMenu() {
    echo "\n==============================================\n";
    echo "   SQ BACKEND - TINKER TESTING MENU\n";
    echo "==============================================\n\n";
    echo "1. Show Statistics\n";
    echo "2. Test User Model\n";
    echo "3. Test Guru Model\n";
    echo "4. Test Siswa Model\n";
    echo "5. Test Kelas Model\n";
    echo "6. Test Hafalan Model\n";
    echo "7. Test TahunAjaran Model\n";
    echo "8. Test Relationships\n";
    echo "9. Test Cache\n";
    echo "10. Test Configuration\n";
    echo "11. Data Integrity Check\n";
    echo "12. Top Performers\n";
    echo "13. Run All Tests\n";
    echo "0. Exit\n\n";
}

function showStatistics() {
    echo "\n=== STATISTICS ===\n";
    try {
        $stats = [
            'Users' => App\Models\User::count(),
            'Guru' => App\Models\Guru::count(),
            'Siswa' => App\Models\Siswa::count(),
            'Kelas' => App\Models\Kelas::count(),
            'Hafalan' => App\Models\Hafalan::count(),
            'Tahun Ajaran' => App\Models\TahunAjaran::count(),
        ];
        
        foreach ($stats as $model => $count) {
            echo "✓ {$model}: {$count}\n";
        }
        
        echo "\n--- Breakdown ---\n";
        echo "Active users: " . App\Models\User::where('is_active', true)->count() . "\n";
        echo "Inactive users: " . App\Models\User::where('is_active', false)->count() . "\n";
        echo "Hafalan today: " . App\Models\Hafalan::whereDate('created_at', today())->count() . "\n";
        echo "Active kelas: " . App\Models\Kelas::where('is_active', true)->count() . "\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function testUserModel() {
    echo "\n=== USER MODEL TEST ===\n";
    try {
        $user = App\Models\User::first();
        if ($user) {
            echo "Sample User:\n";
            echo "  ID: {$user->id}\n";
            echo "  Username: {$user->username}\n";
            echo "  Email: {$user->email}\n";
            echo "  Active: " . ($user->is_active ? 'Yes' : 'No') . "\n";
            echo "  Created: {$user->created_at}\n";
            
            if ($user->guru) {
                echo "  Role: Guru\n";
                echo "  Guru NIP: {$user->guru->nip}\n";
            } elseif ($user->siswa) {
                echo "  Role: Siswa\n";
                echo "  Siswa NISN: " . ($user->siswa->nisn ?? 'N/A') . "\n";
            }
        } else {
            echo "No users found\n";
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function testGuruModel() {
    echo "\n=== GURU MODEL TEST ===\n";
    try {
        $guru = App\Models\Guru::with('user')->first();
        if ($guru) {
            echo "Sample Guru:\n";
            echo "  ID: {$guru->id}\n";
            echo "  Nama: {$guru->nama_lengkap}\n";
            echo "  NIP: {$guru->nip}\n";
            if ($guru->user) {
                echo "  Username: {$guru->user->username}\n";
            }
        } else {
            echo "No guru found\n";
        }
        
        echo "\nTotal Guru: " . App\Models\Guru::count() . "\n";
        echo "Guru with user account: " . App\Models\Guru::has('user')->count() . "\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function testSiswaModel() {
    echo "\n=== SISWA MODEL TEST ===\n";
    try {
        $siswa = App\Models\Siswa::with(['user', 'kelas', 'hafalan'])->first();
        if ($siswa) {
            echo "Sample Siswa:\n";
            echo "  ID: {$siswa->id}\n";
            echo "  Nama: {$siswa->nama_lengkap}\n";
            echo "  NISN: " . ($siswa->nisn ?? 'N/A') . "\n";
            if ($siswa->kelas) {
                echo "  Kelas: {$siswa->kelas->nama}\n";
            }
            echo "  Hafalan count: " . $siswa->hafalan->count() . "\n";
        } else {
            echo "No siswa found\n";
        }
        
        echo "\nTotal Siswa: " . App\Models\Siswa::count() . "\n";
        echo "Siswa with hafalan: " . App\Models\Siswa::has('hafalan')->count() . "\n";
        echo "Siswa without hafalan: " . App\Models\Siswa::doesntHave('hafalan')->count() . "\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function testKelasModel() {
    echo "\n=== KELAS MODEL TEST ===\n";
    try {
        $kelasList = App\Models\Kelas::withCount('siswa')->get();
        echo "Kelas breakdown:\n";
        foreach ($kelasList as $kelas) {
            echo "  - {$kelas->nama}: {$kelas->siswa_count} siswa\n";
        }
        
        $avgSiswa = App\Models\Siswa::count() / max(App\Models\Kelas::count(), 1);
        echo "\nAverage siswa per kelas: " . round($avgSiswa, 2) . "\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function testHafalanModel() {
    echo "\n=== HAFALAN MODEL TEST ===\n";
    try {
        $total = App\Models\Hafalan::count();
        $today = App\Models\Hafalan::whereDate('created_at', today())->count();
        $week = App\Models\Hafalan::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();
        
        echo "Total hafalan: {$total}\n";
        echo "Hafalan today: {$today}\n";
        echo "Hafalan this week: {$week}\n";
        
        $latest = App\Models\Hafalan::with('siswa')->latest()->first();
        if ($latest) {
            $siswaName = $latest->siswa ? $latest->siswa->nama_lengkap : 'Unknown';
            echo "\nLatest hafalan:\n";
            echo "  Surah: {$latest->surah_id}\n";
            echo "  Ayat: {$latest->ayat_dari}-{$latest->ayat_sampai}\n";
            echo "  Siswa: {$siswaName}\n";
            echo "  Status: {$latest->status}\n";
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function testTahunAjaranModel() {
    echo "\n=== TAHUN AJARAN MODEL TEST ===\n";
    try {
        $active = App\Models\TahunAjaran::where('is_active', true)->first();
        if ($active) {
            echo "Active tahun ajaran: {$active->semester} {$active->tahun}\n";
        } else {
            echo "No active tahun ajaran\n";
        }
        
        echo "\nAll tahun ajaran:\n";
        $all = App\Models\TahunAjaran::orderBy('tahun', 'desc')->orderBy('semester', 'desc')->get();
        foreach ($all as $ta) {
            $status = $ta->is_active ? ' (ACTIVE)' : '';
            echo "  - {$ta->semester} {$ta->tahun}{$status}\n";
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function testRelationships() {
    echo "\n=== RELATIONSHIP TEST ===\n";
    try {
        $tests = [];
        
        // User -> Guru
        $tests['User->Guru'] = App\Models\User::has('guru')->count() > 0;
        
        // User -> Siswa
        $tests['User->Siswa'] = App\Models\User::has('siswa')->count() > 0;
        
        // Siswa -> Hafalan
        $tests['Siswa->Hafalan'] = App\Models\Siswa::has('hafalan')->count() > 0;
        
        // Kelas -> Siswa
        $tests['Kelas->Siswa'] = App\Models\Kelas::has('siswa')->count() > 0;
        
        // Kelas -> TahunAjaran
        $kelas = App\Models\Kelas::with('tahunAjaran')->first();
        $tests['Kelas->TahunAjaran'] = $kelas && $kelas->tahunAjaran;
        
        foreach ($tests as $rel => $result) {
            echo ($result ? '✓' : '✗') . " {$rel}\n";
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function testCache() {
    echo "\n=== CACHE TEST ===\n";
    try {
        $key = 'test_' . time();
        $value = 'Test Value ' . rand(1000, 9999);
        
        Cache::put($key, $value, 60);
        $retrieved = Cache::get($key);
        
        if ($retrieved === $value) {
            echo "✓ Cache write/read works\n";
            Cache::forget($key);
            echo "✓ Cache delete works\n";
        } else {
            echo "✗ Cache test failed\n";
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function testConfiguration() {
    echo "\n=== CONFIGURATION TEST ===\n";
    try {
        echo "App name: " . config('app.name') . "\n";
        echo "Environment: " . app()->environment() . "\n";
        echo "Debug: " . (config('app.debug') ? 'ON' : 'OFF') . "\n";
        echo "Timezone: " . config('app.timezone') . "\n";
        echo "Locale: " . config('app.locale') . "\n";
        echo "Current time: " . now()->format('Y-m-d H:i:s') . "\n";
        echo "Cache driver: " . config('cache.default') . "\n";
        echo "Session driver: " . config('session.driver') . "\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function checkDataIntegrity() {
    echo "\n=== DATA INTEGRITY CHECK ===\n";
    try {
        // Users without role
        $orphanUsers = App\Models\User::doesntHave('guru')->doesntHave('siswa')->count();
        if ($orphanUsers > 0) {
            echo "⚠ Users without guru/siswa role: {$orphanUsers}\n";
        } else {
            echo "✓ All users have roles\n";
        }
        
        // Siswa without kelas
        $siswaNoKelas = App\Models\Siswa::whereNull('kelas_id')->count();
        if ($siswaNoKelas > 0) {
            echo "⚠ Siswa without kelas: {$siswaNoKelas}\n";
        } else {
            echo "✓ All siswa have kelas\n";
        }
        
        // Duplicate usernames
        $duplicates = App\Models\User::selectRaw('username, count(*) as cnt')
            ->groupBy('username')
            ->having('cnt', '>', 1)
            ->count();
        
        if ($duplicates > 0) {
            echo "⚠ Duplicate usernames: {$duplicates}\n";
        } else {
            echo "✓ No duplicate usernames\n";
        }
        
        // Kelas without tahun ajaran
        $kelasNoTA = App\Models\Kelas::whereNull('tahun_ajaran_id')->count();
        if ($kelasNoTA > 0) {
            echo "⚠ Kelas without tahun ajaran: {$kelasNoTA}\n";
        } else {
            echo "✓ All kelas have tahun ajaran\n";
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function showTopPerformers() {
    echo "\n=== TOP PERFORMERS ===\n";
    try {
        $topSiswa = App\Models\Siswa::withCount('hafalan')
            ->orderBy('hafalan_count', 'desc')
            ->take(10)
            ->get();
        
        echo "Top 10 siswa by hafalan count:\n";
        foreach ($topSiswa as $index => $siswa) {
            $num = $index + 1;
            $name = $siswa->nama_lengkap ?: 'Unknown';
            echo "  {$num}. {$name}: {$siswa->hafalan_count} hafalan\n";
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function runAllTests() {
    showStatistics();
    testUserModel();
    testGuruModel();
    testSiswaModel();
    testKelasModel();
    testHafalanModel();
    testTahunAjaranModel();
    testRelationships();
    testCache();
    testConfiguration();
    checkDataIntegrity();
    showTopPerformers();
}

// Main loop
if (php_sapi_name() === 'cli') {
    // Check if argument provided
    if ($argc > 1) {
        $choice = $argv[1];
    } else {
        // Interactive mode
        while (true) {
            showMenu();
            echo "Enter choice (0-13): ";
            $choice = trim(fgets(STDIN));
            
            if ($choice === '0') {
                echo "\nGoodbye!\n\n";
                break;
            }
            
            switch ($choice) {
                case '1': showStatistics(); break;
                case '2': testUserModel(); break;
                case '3': testGuruModel(); break;
                case '4': testSiswaModel(); break;
                case '5': testKelasModel(); break;
                case '6': testHafalanModel(); break;
                case '7': testTahunAjaranModel(); break;
                case '8': testRelationships(); break;
                case '9': testCache(); break;
                case '10': testConfiguration(); break;
                case '11': checkDataIntegrity(); break;
                case '12': showTopPerformers(); break;
                case '13': runAllTests(); break;
                default: echo "\nInvalid choice!\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        }
        exit(0);
    }
    
    // Run specific test
    switch ($choice) {
        case '1': showStatistics(); break;
        case '2': testUserModel(); break;
        case '3': testGuruModel(); break;
        case '4': testSiswaModel(); break;
        case '5': testKelasModel(); break;
        case '6': testHafalanModel(); break;
        case '7': testTahunAjaranModel(); break;
        case '8': testRelationships(); break;
        case '9': testCache(); break;
        case '10': testConfiguration(); break;
        case '11': checkDataIntegrity(); break;
        case '12': showTopPerformers(); break;
        case '13': runAllTests(); break;
        default: echo "Invalid choice\n"; exit(1);
    }
}
