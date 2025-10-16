<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Hafalan;

// Enable query logging
DB::enableQueryLog();

echo "========================================\n";
echo "   QUERY PERFORMANCE TEST (N+1 Check)  \n";
echo "========================================\n\n";

echo "Testing: Get hafalan with kelas_id=1 (5 records)\n";
echo "Expected: 3 queries (1 for hafalan, 1 for siswa+kelas, 1 for guru)\n\n";

// Clear query log
DB::flushQueryLog();

// Execute query with eager loading (optimized)
$hafalan = Hafalan::with([
    'siswa' => function ($query) {
        $query->select('id', 'user_id', 'nis', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'alamat', 'kelas_id');
    },
    'siswa.kelas' => function ($query) {
        $query->select('id', 'nama_kelas', 'wali_kelas_id', 'tahun_ajaran');
    },
    'guru' => function ($query) {
        $query->select('id', 'user_id', 'nip', 'nama', 'jenis_kelamin', 'no_hp');
    }
])
->whereHas('siswa', function ($q) {
    $q->where('kelas_id', 1);
})
->limit(5)
->get();

$queries = DB::getQueryLog();

echo "Results found: " . $hafalan->count() . " records\n";
echo "Total queries executed: " . count($queries) . "\n\n";

echo "=== Query Details ===\n";
foreach ($queries as $index => $query) {
    echo ($index + 1) . ". " . $query['query'] . "\n";
    echo "   Bindings: " . json_encode($query['bindings']) . "\n";
    echo "   Time: " . $query['time'] . "ms\n\n";
}

echo "========================================\n";
if (count($queries) <= 4) {
    echo "✓ OPTIMIZED! No N+1 problem detected\n";
    echo "  Queries are efficient with eager loading\n";
} else {
    echo "⚠ WARNING! Possible N+1 problem\n";
    echo "  Expected max 4 queries, got " . count($queries) . "\n";
}
echo "========================================\n\n";

// Test 2: Without optimization (to show difference)
echo "========================================\n";
echo "   COMPARISON: Without Optimization     \n";
echo "========================================\n\n";

DB::flushQueryLog();

// Without eager loading (BAD - will cause N+1)
$hafalanBad = Hafalan::whereHas('siswa', function ($q) {
    $q->where('kelas_id', 1);
})->limit(5)->get();

// Access relationships (this will trigger additional queries)
foreach ($hafalanBad as $h) {
    $siswa = $h->siswa;
    if ($siswa) {
        $kelas = $siswa->kelas;
    }
    $guru = $h->guru;
}

$queriesBad = DB::getQueryLog();

echo "Total queries WITHOUT eager loading: " . count($queriesBad) . "\n";
echo "Difference: +" . (count($queriesBad) - count($queries)) . " extra queries\n\n";

if (count($queriesBad) > count($queries)) {
    echo "⚠ This demonstrates N+1 problem!\n";
    echo "  Without eager loading, Laravel makes separate queries\n";
    echo "  for each relationship access.\n";
}

echo "========================================\n";
echo "         Performance Summary            \n";
echo "========================================\n";
echo "WITH eager loading:    " . count($queries) . " queries\n";
echo "WITHOUT eager loading: " . count($queriesBad) . " queries\n";
echo "Optimization saved:    " . (count($queriesBad) - count($queries)) . " queries\n";
echo "========================================\n";
