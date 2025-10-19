#!/usr/bin/env php
<?php

/**
 * Database Maintenance and Cleanup Script
 * 
 * Run with: php tinker_maintenance.php
 * 
 * This script helps maintain database integrity and cleanup
 * 
 * @phpstan-ignore-next-line
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Laravel Facade type hints for IDE
use Illuminate\Support\Facades\{DB, Cache, Artisan};

echo "\n==============================================\n";
echo "   DATABASE MAINTENANCE & CLEANUP\n";
echo "==============================================\n\n";

function showMenu() {
    echo "\nMaintenance Options:\n";
    echo "1. Clear all caches\n";
    echo "2. Optimize database\n";
    echo "3. Check data integrity\n";
    echo "4. Clean orphan records (preview)\n";
    echo "5. Clean orphan records (execute)\n";
    echo "6. Rebuild cache\n";
    echo "7. Show database statistics\n";
    echo "8. Export data summary\n";
    echo "9. Check for duplicates\n";
    echo "0. Exit\n";
    echo "\nChoice: ";
}

function clearCaches() {
    echo "\n=== CLEARING CACHES ===\n";
    try {
        Artisan::call('config:clear');
        echo "✓ Config cache cleared\n";
        
        Artisan::call('cache:clear');
        echo "✓ Application cache cleared\n";
        
        Artisan::call('route:clear');
        echo "✓ Route cache cleared\n";
        
        Artisan::call('view:clear');
        echo "✓ View cache cleared\n";
        
        echo "\n✓ All caches cleared successfully!\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function optimizeDatabase() {
    echo "\n=== OPTIMIZING DATABASE ===\n";
    try {
        // Get all tables
        $tables = DB::select('SHOW TABLES');
        $dbName = DB::connection()->getDatabaseName();
        
        echo "Optimizing tables...\n";
        foreach ($tables as $table) {
            $tableName = $table->{"Tables_in_{$dbName}"};
            DB::statement("OPTIMIZE TABLE `{$tableName}`");
            echo "✓ Optimized: {$tableName}\n";
        }
        
        echo "\n✓ Database optimization complete!\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function checkDataIntegrity() {
    echo "\n=== DATA INTEGRITY CHECK ===\n";
    $issues = [];
    
    try {
        // Check 1: Users without roles
        $orphanUsers = App\Models\User::doesntHave('guru')
            ->doesntHave('siswa')
            ->count();
        
        if ($orphanUsers > 0) {
            $issues[] = "Users without role: {$orphanUsers}";
            echo "⚠ {$orphanUsers} users without guru/siswa role\n";
        } else {
            echo "✓ All users have roles\n";
        }
        
        // Check 2: Siswa without kelas
        $siswaNoKelas = App\Models\Siswa::whereNull('kelas_id')->count();
        if ($siswaNoKelas > 0) {
            $issues[] = "Siswa without kelas: {$siswaNoKelas}";
            echo "⚠ {$siswaNoKelas} siswa without kelas\n";
        } else {
            echo "✓ All siswa have kelas\n";
        }
        
        // Check 3: Kelas without tahun ajaran
        $kelasNoTA = App\Models\Kelas::whereNull('tahun_ajaran_id')->count();
        if ($kelasNoTA > 0) {
            $issues[] = "Kelas without tahun ajaran: {$kelasNoTA}";
            echo "⚠ {$kelasNoTA} kelas without tahun ajaran\n";
        } else {
            echo "✓ All kelas have tahun ajaran\n";
        }
        
        // Check 4: Hafalan with invalid siswa_id
        $invalidHafalan = App\Models\Hafalan::whereNotIn('siswa_id', 
            App\Models\Siswa::pluck('id')
        )->count();
        
        if ($invalidHafalan > 0) {
            $issues[] = "Hafalan with invalid siswa: {$invalidHafalan}";
            echo "⚠ {$invalidHafalan} hafalan with invalid siswa_id\n";
        } else {
            echo "✓ All hafalan have valid siswa\n";
        }
        
        // Check 5: Duplicate usernames
        $duplicates = App\Models\User::selectRaw('username, count(*) as cnt')
            ->groupBy('username')
            ->having('cnt', '>', 1)
            ->get();
        
        if ($duplicates->count() > 0) {
            $issues[] = "Duplicate usernames: {$duplicates->count()}";
            echo "⚠ Duplicate usernames found:\n";
            foreach ($duplicates as $dup) {
                echo "  - {$dup->username}: {$dup->cnt} times\n";
            }
        } else {
            echo "✓ No duplicate usernames\n";
        }
        
        // Summary
        if (empty($issues)) {
            echo "\n✓ Database integrity is good!\n";
        } else {
            echo "\n⚠ Found " . count($issues) . " integrity issues.\n";
        }
        
        return $issues;
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
        return [];
    }
}

function previewOrphanCleanup() {
    echo "\n=== ORPHAN RECORDS PREVIEW ===\n";
    echo "This will show records that can be cleaned up.\n\n";
    
    try {
        // Orphan users
        $orphanUsers = App\Models\User::doesntHave('guru')
            ->doesntHave('siswa')
            ->get();
        
        if ($orphanUsers->count() > 0) {
            echo "Users without role ({$orphanUsers->count()}):\n";
            foreach ($orphanUsers->take(5) as $user) {
                echo "  - ID {$user->id}: {$user->username}\n";
            }
            if ($orphanUsers->count() > 5) {
                echo "  ... and " . ($orphanUsers->count() - 5) . " more\n";
            }
            echo "\n";
        }
        
        // Siswa without kelas
        $siswaNoKelas = App\Models\Siswa::whereNull('kelas_id')->get();
        if ($siswaNoKelas->count() > 0) {
            echo "Siswa without kelas ({$siswaNoKelas->count()}):\n";
            foreach ($siswaNoKelas->take(5) as $siswa) {
                echo "  - ID {$siswa->id}: {$siswa->nama_lengkap}\n";
            }
            if ($siswaNoKelas->count() > 5) {
                echo "  ... and " . ($siswaNoKelas->count() - 5) . " more\n";
            }
            echo "\n";
        }
        
        if ($orphanUsers->count() === 0 && $siswaNoKelas->count() === 0) {
            echo "✓ No orphan records found!\n";
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function executeOrphanCleanup() {
    echo "\n=== EXECUTING ORPHAN CLEANUP ===\n";
    echo "⚠ WARNING: This will delete records!\n";
    echo "Are you sure? (yes/no): ";
    
    $confirm = trim(fgets(STDIN));
    if (strtolower($confirm) !== 'yes') {
        echo "Cancelled.\n";
        return;
    }
    
    DB::beginTransaction();
    try {
        $deleted = 0;
        
        // Don't actually delete users without roles - they might be admin
        // Just report them
        echo "ℹ Skipping user deletion (may be admin accounts)\n";
        
        // You can add other cleanup logic here
        
        DB::commit();
        echo "✓ Cleanup complete. {$deleted} records processed.\n";
    } catch (Exception $e) {
        DB::rollBack();
        echo "✗ Error: " . $e->getMessage() . "\n";
        echo "✗ Cleanup rolled back.\n";
    }
}

function rebuildCache() {
    echo "\n=== REBUILDING CACHE ===\n";
    try {
        // Clear first
        Cache::flush();
        echo "✓ Cache cleared\n";
        
        // Rebuild common caches
        echo "Building caches...\n";
        
        Cache::remember('stats_users', 3600, function() {
            return App\Models\User::count();
        });
        echo "✓ User statistics cached\n";
        
        Cache::remember('stats_hafalan', 3600, function() {
            return App\Models\Hafalan::count();
        });
        echo "✓ Hafalan statistics cached\n";
        
        Cache::remember('active_tahun_ajaran', 3600, function() {
            return App\Models\TahunAjaran::where('is_active', true)->first();
        });
        echo "✓ Active tahun ajaran cached\n";
        
        echo "\n✓ Cache rebuild complete!\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function showDatabaseStatistics() {
    echo "\n=== DATABASE STATISTICS ===\n";
    try {
        $dbName = DB::connection()->getDatabaseName();
        echo "Database: {$dbName}\n\n";
        
        // Table sizes
        $tables = DB::select("
            SELECT 
                table_name AS 'table',
                table_rows AS 'rows',
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb'
            FROM information_schema.TABLES
            WHERE table_schema = ?
            ORDER BY (data_length + index_length) DESC
        ", [$dbName]);
        
        echo "Table Statistics:\n";
        echo str_pad("Table", 30) . str_pad("Rows", 10) . "Size (MB)\n";
        echo str_repeat("-", 50) . "\n";
        
        foreach ($tables as $table) {
            echo str_pad($table->table, 30) 
                . str_pad($table->rows, 10) 
                . $table->size_mb . "\n";
        }
        
        // Total database size
        $totalSize = DB::selectOne("
            SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.TABLES
            WHERE table_schema = ?
        ", [$dbName]);
        
        echo str_repeat("-", 50) . "\n";
        echo "Total Database Size: {$totalSize->size_mb} MB\n";
        
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function exportDataSummary() {
    echo "\n=== EXPORTING DATA SUMMARY ===\n";
    try {
        $summary = [
            'generated_at' => now()->toDateTimeString(),
            'database' => DB::connection()->getDatabaseName(),
            'statistics' => [
                'users' => App\Models\User::count(),
                'active_users' => App\Models\User::where('is_active', true)->count(),
                'guru' => App\Models\Guru::count(),
                'siswa' => App\Models\Siswa::count(),
                'kelas' => App\Models\Kelas::count(),
                'hafalan' => App\Models\Hafalan::count(),
                'tahun_ajaran' => App\Models\TahunAjaran::count(),
            ],
            'hafalan_breakdown' => App\Models\Hafalan::selectRaw('surah_id, count(*) as total')
                ->groupBy('surah_id')
                ->get()
                ->toArray(),
            'kelas_distribution' => App\Models\Kelas::withCount('siswa')
                ->get()
                ->map(function($kelas) {
                    return [
                        'nama' => $kelas->nama,
                        'siswa_count' => $kelas->siswa_count
                    ];
                })
                ->toArray(),
        ];
        
        $filename = 'data_summary_' . date('Y-m-d_His') . '.json';
        file_put_contents($filename, json_encode($summary, JSON_PRETTY_PRINT));
        
        echo "✓ Summary exported to: {$filename}\n";
        echo "✓ Total size: " . filesize($filename) . " bytes\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

function checkDuplicates() {
    echo "\n=== CHECKING FOR DUPLICATES ===\n";
    try {
        // Check duplicate usernames
        $dupUsernames = App\Models\User::selectRaw('username, count(*) as cnt')
            ->groupBy('username')
            ->having('cnt', '>', 1)
            ->get();
        
        if ($dupUsernames->count() > 0) {
            echo "⚠ Duplicate usernames:\n";
            foreach ($dupUsernames as $dup) {
                echo "  - {$dup->username}: {$dup->cnt} times\n";
            }
        } else {
            echo "✓ No duplicate usernames\n";
        }
        
        // Check duplicate emails
        $dupEmails = App\Models\User::selectRaw('email, count(*) as cnt')
            ->groupBy('email')
            ->having('cnt', '>', 1)
            ->get();
        
        if ($dupEmails->count() > 0) {
            echo "⚠ Duplicate emails:\n";
            foreach ($dupEmails as $dup) {
                echo "  - {$dup->email}: {$dup->cnt} times\n";
            }
        } else {
            echo "✓ No duplicate emails\n";
        }
        
        // Check duplicate NIP
        $dupNIP = App\Models\Guru::selectRaw('nip, count(*) as cnt')
            ->whereNotNull('nip')
            ->groupBy('nip')
            ->having('cnt', '>', 1)
            ->get();
        
        if ($dupNIP->count() > 0) {
            echo "⚠ Duplicate NIP:\n";
            foreach ($dupNIP as $dup) {
                echo "  - {$dup->nip}: {$dup->cnt} times\n";
            }
        } else {
            echo "✓ No duplicate NIP\n";
        }
        
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

// Main loop
if (php_sapi_name() === 'cli') {
    while (true) {
        showMenu();
        $choice = trim(fgets(STDIN));
        
        if ($choice === '0') {
            echo "\nGoodbye!\n\n";
            break;
        }
        
        switch ($choice) {
            case '1': clearCaches(); break;
            case '2': optimizeDatabase(); break;
            case '3': checkDataIntegrity(); break;
            case '4': previewOrphanCleanup(); break;
            case '5': executeOrphanCleanup(); break;
            case '6': rebuildCache(); break;
            case '7': showDatabaseStatistics(); break;
            case '8': exportDataSummary(); break;
            case '9': checkDuplicates(); break;
            default: echo "\nInvalid choice!\n";
        }
        
        echo "\nPress Enter to continue...";
        fgets(STDIN);
    }
}
