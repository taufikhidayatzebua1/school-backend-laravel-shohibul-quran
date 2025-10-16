<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "         DATABASE VERIFICATION          \n";
echo "========================================\n\n";

echo "Total Users: " . DB::table('users')->count() . "\n";
echo "Total Guru: " . DB::table('guru')->count() . "\n";
echo "Total Kelas: " . DB::table('kelas')->count() . "\n";
echo "Total Siswa: " . DB::table('siswa')->count() . "\n";
echo "Total Hafalan: " . DB::table('hafalan')->count() . "\n\n";

echo "=== Users by Role ===\n";
$roles = ['super-admin', 'admin', 'kepala-sekolah', 'guru', 'tata-usaha', 'yayasan', 'siswa', 'orang-tua'];
foreach ($roles as $role) {
    $count = DB::table('users')->where('role', $role)->count();
    echo ucwords(str_replace('-', ' ', $role)) . ": $count\n";
}

echo "\n=== Sample Guru (3 records) ===\n";
$gurus = DB::table('guru')
    ->join('users', 'guru.user_id', '=', 'users.id')
    ->select('guru.nama', 'guru.nip', 'users.email', 'users.role')
    ->limit(3)
    ->get();

foreach ($gurus as $guru) {
    echo "$guru->nama | NIP: $guru->nip | Email: $guru->email | Role: $guru->role\n";
}

echo "\n=== Sample Siswa (3 records) ===\n";
$siswas = DB::table('siswa')
    ->join('users', 'siswa.user_id', '=', 'users.id')
    ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
    ->select('siswa.nama', 'siswa.nis', 'kelas.nama_kelas', 'users.email', 'users.role')
    ->limit(3)
    ->get();

foreach ($siswas as $siswa) {
    echo "$siswa->nama | NIS: $siswa->nis | Kelas: $siswa->nama_kelas | Email: $siswa->email | Role: $siswa->role\n";
}

echo "\n=== Sample Other Roles (5 records) ===\n";
$others = DB::table('users')
    ->whereNotIn('role', ['guru', 'siswa'])
    ->select('name', 'email', 'role')
    ->limit(5)
    ->get();

foreach ($others as $user) {
    echo "$user->name | Email: $user->email | Role: $user->role\n";
}

echo "\n=== Sample Hafalan (5 records) ===\n";
$hafalans = DB::table('hafalan')
    ->join('siswa', 'hafalan.siswa_id', '=', 'siswa.id')
    ->join('guru', 'hafalan.guru_id', '=', 'guru.id')
    ->select(
        'siswa.nama as nama_siswa',
        'guru.nama as nama_guru',
        'hafalan.surah_id',
        'hafalan.ayat_dari',
        'hafalan.ayat_sampai',
        'hafalan.status',
        'hafalan.tanggal'
    )
    ->limit(5)
    ->get();

foreach ($hafalans as $hafalan) {
    echo "Siswa: $hafalan->nama_siswa | Guru: $hafalan->nama_guru | Surah: $hafalan->surah_id | Ayat: $hafalan->ayat_dari-$hafalan->ayat_sampai | Status: $hafalan->status | Tanggal: $hafalan->tanggal\n";
}

echo "\n=== Hafalan by Status ===\n";
$statusCounts = DB::table('hafalan')
    ->select('status', DB::raw('count(*) as total'))
    ->groupBy('status')
    ->get();

foreach ($statusCounts as $status) {
    echo ucfirst($status->status) . ": $status->total\n";
}

echo "\n========================================\n";
echo "      Verification Complete! ✓          \n";
echo "========================================\n";
