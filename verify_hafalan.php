<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "    HAFALAN STATISTICS & DETAILS        \n";
echo "========================================\n\n";

// Overall Statistics
echo "=== Overall Statistics ===\n";
$totalHafalan = DB::table('hafalan')->count();
$totalSiswa = DB::table('siswa')->count();
$avgPerSiswa = round($totalHafalan / $totalSiswa, 1);

echo "Total Hafalan: $totalHafalan\n";
echo "Total Siswa: $totalSiswa\n";
echo "Average Hafalan per Siswa: $avgPerSiswa\n\n";

// Status Distribution
echo "=== Hafalan by Status ===\n";
$statusCounts = DB::table('hafalan')
    ->select('status', DB::raw('count(*) as total'))
    ->groupBy('status')
    ->get();

foreach ($statusCounts as $status) {
    $percentage = round(($status->total / $totalHafalan) * 100, 1);
    echo ucfirst($status->status) . ": $status->total ($percentage%)\n";
}

// Top Students
echo "\n=== Top 5 Siswa by Hafalan Count ===\n";
$topSiswa = DB::table('hafalan')
    ->join('siswa', 'hafalan.siswa_id', '=', 'siswa.id')
    ->select('siswa.nama', 'siswa.nis', DB::raw('count(*) as total_hafalan'))
    ->groupBy('siswa.id', 'siswa.nama', 'siswa.nis')
    ->orderBy('total_hafalan', 'desc')
    ->limit(5)
    ->get();

$rank = 1;
foreach ($topSiswa as $siswa) {
    echo "$rank. $siswa->nama (NIS: $siswa->nis) - $siswa->total_hafalan hafalan\n";
    $rank++;
}

// Most Active Guru
echo "\n=== Top 5 Guru Pembimbing by Hafalan Count ===\n";
$topGuru = DB::table('hafalan')
    ->join('guru', 'hafalan.guru_id', '=', 'guru.id')
    ->select('guru.nama', 'guru.nip', DB::raw('count(*) as total_bimbingan'))
    ->groupBy('guru.id', 'guru.nama', 'guru.nip')
    ->orderBy('total_bimbingan', 'desc')
    ->limit(5)
    ->get();

$rank = 1;
foreach ($topGuru as $guru) {
    echo "$rank. $guru->nama (NIP: $guru->nip) - $guru->total_bimbingan bimbingan\n";
    $rank++;
}

// Most Popular Surah
echo "\n=== Top 10 Surah yang Paling Banyak Dihafal ===\n";
$surahNames = [
    1 => 'Al-Fatihah', 78 => 'An-Naba', 79 => 'An-Naziat', 80 => 'Abasa',
    81 => 'At-Takwir', 82 => 'Al-Infitar', 83 => 'Al-Mutaffifin', 84 => 'Al-Insyiqaq',
    85 => 'Al-Buruj', 86 => 'At-Tariq', 87 => 'Al-A\'la', 88 => 'Al-Ghasyiyah',
    89 => 'Al-Fajr', 90 => 'Al-Balad', 91 => 'Asy-Syams', 92 => 'Al-Lail',
    93 => 'Ad-Dhuha', 94 => 'Asy-Syarh', 95 => 'At-Tin', 96 => 'Al-Alaq',
    97 => 'Al-Qadr', 98 => 'Al-Bayyinah', 99 => 'Az-Zalzalah', 100 => 'Al-Adiyat',
    101 => 'Al-Qari\'ah', 102 => 'At-Takasur', 103 => 'Al-Asr', 104 => 'Al-Humazah',
    105 => 'Al-Fil', 106 => 'Quraisy', 107 => 'Al-Ma\'un', 108 => 'Al-Kausar',
    109 => 'Al-Kafirun', 110 => 'An-Nasr', 111 => 'Al-Lahab', 112 => 'Al-Ikhlas',
    113 => 'Al-Falaq', 114 => 'An-Nas'
];

$topSurah = DB::table('hafalan')
    ->select('surah_id', DB::raw('count(*) as total'))
    ->groupBy('surah_id')
    ->orderBy('total', 'desc')
    ->limit(10)
    ->get();

$rank = 1;
foreach ($topSurah as $surah) {
    $surahName = $surahNames[$surah->surah_id] ?? "Surah $surah->surah_id";
    echo "$rank. $surahName (Surah $surah->surah_id) - $surah->total kali dihafal\n";
    $rank++;
}

// Recent Hafalan
echo "\n=== 5 Hafalan Terbaru ===\n";
$recentHafalan = DB::table('hafalan')
    ->join('siswa', 'hafalan.siswa_id', '=', 'siswa.id')
    ->join('guru', 'hafalan.guru_id', '=', 'guru.id')
    ->select(
        'siswa.nama as nama_siswa',
        'guru.nama as nama_guru',
        'hafalan.surah_id',
        'hafalan.ayat_dari',
        'hafalan.ayat_sampai',
        'hafalan.status',
        'hafalan.tanggal',
        'hafalan.keterangan'
    )
    ->orderBy('hafalan.tanggal', 'desc')
    ->orderBy('hafalan.created_at', 'desc')
    ->limit(5)
    ->get();

foreach ($recentHafalan as $hafalan) {
    $surahName = $surahNames[$hafalan->surah_id] ?? "Surah $hafalan->surah_id";
    echo "- $hafalan->nama_siswa | $surahName ($hafalan->ayat_dari-$hafalan->ayat_sampai)\n";
    echo "  Guru: $hafalan->nama_guru | Status: $hafalan->status | Tanggal: $hafalan->tanggal\n";
    echo "  Keterangan: $hafalan->keterangan\n\n";
}

echo "========================================\n";
echo "   Hafalan Verification Complete! ✓    \n";
echo "========================================\n";
