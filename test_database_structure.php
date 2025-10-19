<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n=== TEST STRUKTUR DATABASE BARU ===\n\n";

// Test Tahun Ajaran
echo "1. TAHUN AJARAN\n";
echo str_repeat("-", 50) . "\n";
$tahunAjaran = \App\Models\TahunAjaran::all();
foreach ($tahunAjaran as $ta) {
    $status = $ta->is_active ? '[AKTIF]' : '';
    echo "  - {$ta->semester} {$ta->tahun} {$status}\n";
}
echo "  Total: {$tahunAjaran->count()} tahun ajaran\n";
echo "  Aktif: " . \App\Models\TahunAjaran::where('is_active', true)->count() . "\n\n";

// Test Kelas
echo "2. KELAS\n";
echo str_repeat("-", 50) . "\n";
$kelas = \App\Models\Kelas::with(['tahunAjaran', 'waliKelas'])->get();
foreach ($kelas as $k) {
    $ta = $k->tahunAjaran ? "{$k->tahunAjaran->semester} {$k->tahunAjaran->tahun}" : 'Tidak ada';
    $wali = $k->waliKelas ? $k->waliKelas->nama : 'Tidak ada';
    $ruangan = $k->ruangan ?: 'Tidak ada ruangan';
    echo "  - {$k->nama} ({$ruangan}) | TA: {$ta} | Wali: {$wali}\n";
}
echo "  Total: {$kelas->count()} kelas\n\n";

// Test Siswa dengan Kelas baru
echo "3. SISWA (5 data pertama)\n";
echo str_repeat("-", 50) . "\n";
$siswa = \App\Models\Siswa::with('kelas')->take(5)->get();
foreach ($siswa as $s) {
    $kelasNama = $s->kelas ? "{$s->kelas->nama} - {$s->kelas->ruangan}" : 'Tidak ada kelas';
    echo "  - {$s->nama} ({$s->nis}) | Kelas: {$kelasNama}\n";
}

// Test is_active enforcement untuk Tahun Ajaran
echo "\n4. TEST IS_ACTIVE ENFORCEMENT\n";
echo str_repeat("-", 50) . "\n";
$activeBefore = \App\Models\TahunAjaran::where('is_active', true)->first();
echo "  Sebelum: {$activeBefore->semester} {$activeBefore->tahun} (ID: {$activeBefore->id}) = AKTIF\n";

// Try to activate another tahun ajaran
$another = \App\Models\TahunAjaran::where('id', '!=', $activeBefore->id)->first();
$another->update(['is_active' => true]);

$activeAfter = \App\Models\TahunAjaran::where('is_active', true)->get();
echo "  Sesudah activate ID {$another->id}:\n";
foreach ($activeAfter as $ta) {
    $status = $ta->is_active ? 'AKTIF' : 'NON-AKTIF';
    echo "    - ID {$ta->id}: {$ta->semester} {$ta->tahun} = {$status}\n";
}
echo "  Total Aktif: {$activeAfter->count()} (Harus hanya 1!)\n";

echo "\n=== TEST SELESAI ===\n\n";
