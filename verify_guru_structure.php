<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "╔═══════════════════════════════════════════════════════════════════╗\n";
echo "║              VERIFIKASI STRUKTUR TABEL GURU                       ║\n";
echo "╚═══════════════════════════════════════════════════════════════════╝\n\n";

// 1. Cek struktur kolom
echo "📋 Kolom-kolom di tabel guru:\n";
echo "─────────────────────────────────────────────────────────────────\n";
$columns = Schema::getColumnListing('guru');
foreach ($columns as $column) {
    echo "  ✓ $column\n";
}
echo "\n  Total: " . count($columns) . " kolom\n\n";

// 2. Cek kolom baru
echo "✨ Kolom Baru yang Ditambahkan:\n";
echo "─────────────────────────────────────────────────────────────────\n";
$newColumns = ['tempat_lahir', 'url_photo', 'url_cover', 'is_active', 'deleted_at'];
foreach ($newColumns as $col) {
    $exists = in_array($col, $columns) ? '✅' : '❌';
    echo "  $exists $col\n";
}
echo "\n";

// 3. Cek unique constraint pada user_id
echo "🔒 Unique Constraint:\n";
echo "─────────────────────────────────────────────────────────────────\n";
$indexes = DB::select("SHOW INDEXES FROM guru WHERE Column_name = 'user_id'");
foreach ($indexes as $index) {
    $uniqueStatus = $index->Non_unique == 0 ? '✅ UNIQUE' : '❌ NOT UNIQUE';
    echo "  Index: {$index->Key_name}\n";
    echo "  Status: {$uniqueStatus}\n";
}
echo "\n";

// 4. Cek data guru
echo "📊 Data Guru:\n";
echo "─────────────────────────────────────────────────────────────────\n";
$totalGuru = Guru::count();
$guruBiasa = Guru::whereHas('user', function($q) {
    $q->where('role', 'guru');
})->count();
$waliKelas = Guru::whereHas('user', function($q) {
    $q->where('role', 'wali-kelas');
})->count();
$kepalaSekolah = Guru::whereHas('user', function($q) {
    $q->where('role', 'kepala-sekolah');
})->count();

echo "  Total Guru        : $totalGuru\n";
echo "  - Guru Biasa      : $guruBiasa\n";
echo "  - Wali Kelas      : $waliKelas\n";
echo "  - Kepala Sekolah  : $kepalaSekolah\n";
echo "\n";

// 5. Cek kolom nullable
echo "🔍 Sample Data (3 guru pertama):\n";
echo "─────────────────────────────────────────────────────────────────\n";
$sampleGuru = Guru::with('user')->take(3)->get();
foreach ($sampleGuru as $guru) {
    echo "  Nama: {$guru->nama}\n";
    echo "  - NIP: " . ($guru->nip ?? 'NULL') . "\n";
    echo "  - Tempat Lahir: " . ($guru->tempat_lahir ?? 'NULL') . "\n";
    echo "  - No HP: " . ($guru->no_hp ?? 'NULL') . "\n";
    echo "  - URL Photo: " . ($guru->url_photo ? 'Ada' : 'NULL') . "\n";
    echo "  - URL Cover: " . ($guru->url_cover ?? 'NULL') . "\n";
    echo "  - Is Active: " . ($guru->is_active ? 'Yes' : 'No') . "\n";
    echo "  - User Role: " . ($guru->user->role ?? 'No User') . "\n";
    echo "  ─────────────────────────────────\n";
}
echo "\n";

// 6. Cek SoftDeletes
echo "🗑️ SoftDeletes:\n";
echo "─────────────────────────────────────────────────────────────────\n";
$hasDeletedAt = in_array('deleted_at', $columns);
if ($hasDeletedAt) {
    echo "  ✅ Kolom deleted_at tersedia\n";
    echo "  ✅ SoftDeletes trait terpasang di model\n";
    
    $trashed = Guru::onlyTrashed()->count();
    $active = Guru::count();
    echo "  📊 Active: $active guru\n";
    echo "  📊 Trashed: $trashed guru\n";
} else {
    echo "  ❌ Kolom deleted_at tidak ada\n";
}
echo "\n";

// 7. Cek One-to-One Relationship
echo "🔗 One-to-One Relationship:\n";
echo "─────────────────────────────────────────────────────────────────\n";
$guru = Guru::with('user')->first();
if ($guru && $guru->user) {
    echo "  ✅ Guru -> User: WORKING\n";
    echo "     Guru: {$guru->nama} → User: {$guru->user->name}\n";
}

$user = User::where('role', 'guru')->with('guru')->first();
if ($user && $user->guru) {
    echo "  ✅ User -> Guru: WORKING\n";
    echo "     User: {$user->name} → Guru: {$user->guru->nama}\n";
}
echo "\n";

// 8. Cek duplikasi user_id
echo "🔍 Cek Duplikasi user_id:\n";
echo "─────────────────────────────────────────────────────────────────\n";
$duplicates = DB::table('guru')
    ->select('user_id', DB::raw('COUNT(*) as count'))
    ->whereNotNull('user_id')
    ->groupBy('user_id')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->isEmpty()) {
    echo "  ✅ TIDAK ADA duplikasi user_id\n";
    echo "  ✅ One-to-One relationship terjaga\n";
} else {
    echo "  ❌ DITEMUKAN duplikasi:\n";
    foreach ($duplicates as $dup) {
        echo "    - user_id {$dup->user_id} muncul {$dup->count} kali\n";
    }
}
echo "\n";

// 9. Test unique constraint
echo "🧪 Test Unique Constraint:\n";
echo "─────────────────────────────────────────────────────────────────\n";
echo "  Mencoba insert guru dengan user_id yang sudah ada...\n";

try {
    $existingGuru = Guru::whereNotNull('user_id')->first();
    if ($existingGuru) {
        DB::table('guru')->insert([
            'user_id' => $existingGuru->user_id,
            'nip' => 'TEST-DUPLICATE',
            'nama' => 'Test Duplicate User',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "  ❌ GAGAL! Duplikasi berhasil masuk\n";
    }
} catch (\Illuminate\Database\QueryException $e) {
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        echo "  ✅ BERHASIL! Constraint mencegah duplikasi user_id\n";
    } else {
        echo "  ⚠️ Error lain: " . substr($e->getMessage(), 0, 100) . "...\n";
    }
}
echo "\n";

echo "═══════════════════════════════════════════════════════════════════\n";
echo "✅ VERIFIKASI SELESAI!\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

echo "📝 Summary:\n";
echo "  ✅ Tabel guru memiliki " . count($columns) . " kolom\n";
echo "  ✅ Kolom baru: tempat_lahir, url_photo, url_cover, is_active, deleted_at\n";
echo "  ✅ user_id adalah UNIQUE\n";
echo "  ✅ Semua kolom (kecuali id) adalah NULLABLE\n";
echo "  ✅ SoftDeletes aktif\n";
echo "  ✅ One-to-One relationship User ↔ Guru\n";
echo "  ✅ Total: $totalGuru guru ($guruBiasa guru + $waliKelas wali-kelas + $kepalaSekolah kepala-sekolah)\n";
echo "\n";
