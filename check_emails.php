<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== AVAILABLE USER EMAILS ===\n\n";

echo "GURU EMAILS:\n";
$gurus = DB::table('users')->where('role', 'guru')->limit(5)->get(['email']);
foreach ($gurus as $guru) {
    echo "  - " . $guru->email . "\n";
}

echo "\nSISWA EMAILS:\n";
$siswas = DB::table('users')->where('role', 'siswa')->limit(5)->get(['email']);
foreach ($siswas as $siswa) {
    echo "  - " . $siswa->email . "\n";
}

echo "\nKEPALA SEKOLAH EMAILS:\n";
$kepalas = DB::table('users')->where('role', 'kepala-sekolah')->limit(5)->get(['email']);
foreach ($kepalas as $kepala) {
    echo "  - " . $kepala->email . "\n";
}

echo "\nOTHER ROLES:\n";
$others = DB::table('users')->whereNotIn('role', ['guru', 'siswa', 'kepala-sekolah'])->limit(5)->get(['email', 'role']);
foreach ($others as $other) {
    echo "  - " . $other->email . " (" . $other->role . ")\n";
}
