<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;

echo "╔═══════════════════════════════════════════════════════════════════╗\n";
echo "║         VERIFIKASI USER TESTING (taufikhizet1350@gmail.com)      ║\n";
echo "╚═══════════════════════════════════════════════════════════════════╝\n\n";

$user = User::where('email', 'taufikhizet1350@gmail.com')->first();

if ($user) {
    echo "✓ User ditemukan di tabel users\n";
    echo "  - ID: {$user->id}\n";
    echo "  - Name: {$user->name}\n";
    echo "  - Email: {$user->email}\n";
    echo "  - Role: {$user->role}\n\n";

    $siswa = Siswa::where('user_id', $user->id)->first();
    if ($siswa) {
        echo "✓ User ada di tabel siswa\n";
        echo "  - Siswa ID: {$siswa->id}\n";
        echo "  - NIS: {$siswa->nis}\n";
        echo "  - Nama: {$siswa->nama}\n";
        echo "  - Kelas ID: {$siswa->kelas_id}\n\n";
    } else {
        echo "✗ User TIDAK ada di tabel siswa\n\n";
    }

    $guru = Guru::where('user_id', $user->id)->first();
    if ($guru) {
        echo "✓ User ada di tabel guru\n";
        echo "  - Guru ID: {$guru->id}\n";
        echo "  - NIP: {$guru->nip}\n";
        echo "  - Nama: {$guru->nama}\n\n";
    } else {
        echo "✗ User TIDAK ada di tabel guru\n\n";
    }

    echo "═══════════════════════════════════════════════════════════════════\n";
    echo "STATUS: User testing SIAP digunakan!\n";
    echo "═══════════════════════════════════════════════════════════════════\n\n";
    
    echo "Cara switch role:\n";
    echo "1. php artisan tinker\n";
    echo "2. \$u = User::find({$user->id});\n";
    echo "3. \$u->role = 'guru'; \$u->save();  // ganti role sesuai kebutuhan\n";
    echo "\nRole yang tersedia:\n";
    echo "- siswa, orang-tua, guru, wali-kelas, kepala-sekolah\n";
    echo "- tata-usaha, yayasan, admin, super-admin\n";
} else {
    echo "✗ User TIDAK ditemukan!\n";
}
