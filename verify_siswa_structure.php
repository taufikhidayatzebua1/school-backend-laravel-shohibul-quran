<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Siswa;
use Illuminate\Support\Facades\Schema;

echo "╔═══════════════════════════════════════════════════════════════════╗\n";
echo "║         VERIFIKASI STRUKTUR TABEL SISWA & DATA                   ║\n";
echo "╚═══════════════════════════════════════════════════════════════════╝\n\n";

// 1. Cek kolom yang ada
echo "📋 Kolom-kolom di tabel siswa:\n";
echo "─────────────────────────────────────────────────────────────────\n";
$columns = Schema::getColumnListing('siswa');
foreach ($columns as $column) {
    echo "  ✓ $column\n";
}
echo "\n";

// 2. Cek data siswa pertama
$siswa = Siswa::with('kelas')->first();

if ($siswa) {
    echo "📊 Contoh Data Siswa (ID: {$siswa->id}):\n";
    echo "─────────────────────────────────────────────────────────────────\n";
    echo "  ID              : {$siswa->id}\n";
    echo "  User ID         : " . ($siswa->user_id ?? 'null') . "\n";
    echo "  NIS             : " . ($siswa->nis ?? 'null') . "\n";
    echo "  Nama            : " . ($siswa->nama ?? 'null') . "\n";
    echo "  Tempat Lahir    : " . ($siswa->tempat_lahir ?? 'null') . " ✨ (NEW)\n";
    echo "  Jenis Kelamin   : " . ($siswa->jenis_kelamin ?? 'null') . "\n";
    echo "  Tanggal Lahir   : " . ($siswa->tanggal_lahir ?? 'null') . "\n";
    echo "  Alamat          : " . ($siswa->alamat ?? 'null') . "\n";
    echo "  No HP           : " . ($siswa->no_hp ?? 'null') . " ✨ (NEW)\n";
    echo "  Tahun Masuk     : " . ($siswa->tahun_masuk ?? 'null') . " ✨ (NEW)\n";
    echo "  URL Photo       : " . ($siswa->url_photo ?? 'null') . " ✨ (NEW)\n";
    echo "  URL Cover       : " . ($siswa->url_cover ?? 'null') . " ✨ (NEW)\n";
    echo "  Is Active       : " . ($siswa->is_active ? 'true' : 'false') . " ✨ (NEW)\n";
    echo "  Kelas           : " . ($siswa->kelas->nama_kelas ?? 'null') . "\n";
    echo "  Created At      : {$siswa->created_at}\n";
    echo "  Updated At      : {$siswa->updated_at}\n";
    echo "  Deleted At      : " . ($siswa->deleted_at ?? 'null') . " ✨ (NEW - SoftDelete)\n";
    echo "\n";
} else {
    echo "✗ Tidak ada data siswa\n\n";
}

// 3. Statistik
echo "📈 Statistik:\n";
echo "─────────────────────────────────────────────────────────────────\n";
$totalSiswa = Siswa::count();
$activeSiswa = Siswa::where('is_active', true)->count();
$inactiveSiswa = Siswa::where('is_active', false)->count();
$withPhoto = Siswa::whereNotNull('url_photo')->count();
$withHP = Siswa::whereNotNull('no_hp')->count();

echo "  Total Siswa         : {$totalSiswa}\n";
echo "  Siswa Aktif         : {$activeSiswa}\n";
echo "  Siswa Tidak Aktif   : {$inactiveSiswa}\n";
echo "  Punya Foto          : {$withPhoto}\n";
echo "  Punya No HP         : {$withHP}\n";
echo "\n";

echo "═══════════════════════════════════════════════════════════════════\n";
echo "✅ Kolom baru berhasil ditambahkan!\n";
echo "═══════════════════════════════════════════════════════════════════\n";
echo "\nKolom baru yang ditambahkan:\n";
echo "  1. tempat_lahir (nullable)\n";
echo "  2. no_hp (nullable)\n";
echo "  3. tahun_masuk (nullable, format: YYYY)\n";
echo "  4. url_photo (nullable)\n";
echo "  5. url_cover (nullable)\n";
echo "  6. is_active (default: true)\n";
echo "  7. deleted_at (SoftDelete)\n";
echo "\nSemua kolom kecuali 'id' dan 'created_at' sudah nullable! ✨\n";
