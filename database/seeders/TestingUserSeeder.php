<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestingUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder khusus untuk user testing yang bisa switch role.
     * User ini akan ada di tabel siswa DAN guru.
     * 
     * Email: taufikhizet1350@gmail.com
     * Password: password123
     * 
     * Cara testing:
     * 1. Login dengan email taufikhizet1350@gmail.com
     * 2. Untuk test role berbeda, update role di database:
     *    UPDATE users SET role = 'guru' WHERE email = 'taufikhizet1350@gmail.com';
     *    UPDATE users SET role = 'siswa' WHERE email = 'taufikhizet1350@gmail.com';
     *    UPDATE users SET role = 'wali-kelas' WHERE email = 'taufikhizet1350@gmail.com';
     *    dll.
     */
    public function run(): void
    {
        // 1. Buat user dengan role default siswa
        $userId = DB::table('users')->insertGetId([
            'name' => 'Taufik Hizet (Testing User)',
            'username' => 'taufikhizet',
            'email' => 'taufikhizet1350@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'siswa', // Default role, bisa diubah-ubah
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Insert ke tabel SISWA (untuk testing POV siswa)
        // Ambil kelas pertama yang ada
        $kelasId = DB::table('kelas')->first()->id ?? null;
        
        if ($kelasId) {
            $siswaId = DB::table('siswa')->insertGetId([
                'user_id' => $userId,
                'nis' => '9999999999', // NIS khusus testing
                'nama' => 'Taufik Hizet',
                'tempat_lahir' => 'Jakarta',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2000-01-01',
                'alamat' => 'Alamat Testing',
                'no_hp' => '081234567890',
                'tahun_masuk' => date('Y'),
                'url_photo' => 'https://ui-avatars.com/api/?name=Taufik+Hizet&background=4F46E5',
                'url_cover' => null,
                'is_active' => true,
                'kelas_id' => $kelasId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Insert ke tabel GURU (untuk testing POV guru/wali-kelas/kepala-sekolah)
        $guruId = DB::table('guru')->insertGetId([
            'user_id' => $userId,
            'nip' => '199999999999999999', // NIP khusus testing
            'nama' => 'Taufik Hizet, S.Pd',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2000-01-01',
            'alamat' => 'Alamat Testing',
            'no_hp' => '081234567890',
            'url_photo' => 'https://ui-avatars.com/api/?name=Taufik+Hizet&background=DC2626&color=fff&size=200',
            'url_cover' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "\n";
        echo "╔═══════════════════════════════════════════════════════════════════╗\n";
        echo "║              ✓ TESTING USER BERHASIL DIBUAT!                     ║\n";
        echo "╠═══════════════════════════════════════════════════════════════════╣\n";
        echo "║  Email    : taufikhizet1350@gmail.com                            ║\n";
        echo "║  Password : password123                                           ║\n";
        echo "║  User ID  : $userId                                                    ║\n";
        echo "║  Siswa ID : " . ($siswaId ?? 'N/A') . "                                                   ║\n";
        echo "║  Guru ID  : $guruId                                                   ║\n";
        echo "╠═══════════════════════════════════════════════════════════════════╣\n";
        echo "║  STATUS:                                                          ║\n";
        echo "║  ✓ Ada di tabel users                                             ║\n";
        echo "║  ✓ Ada di tabel siswa (untuk testing POV siswa)                  ║\n";
        echo "║  ✓ Ada di tabel guru (untuk testing POV guru/wali/kepsek)        ║\n";
        echo "╠═══════════════════════════════════════════════════════════════════╣\n";
        echo "║  CARA TESTING SWITCH ROLE:                                        ║\n";
        echo "║                                                                   ║\n";
        echo "║  Akses Tinker:                                                    ║\n";
        echo "║  php artisan tinker                                               ║\n";
        echo "║                                                                   ║\n";
        echo "║  Switch ke role siswa:                                            ║\n";
        echo "║  \$u = User::where('email', 'taufikhizet1350@gmail.com')->first();║\n";
        echo "║  \$u->role = 'siswa'; \$u->save();                                  ║\n";
        echo "║                                                                   ║\n";
        echo "║  Switch ke role guru:                                             ║\n";
        echo "║  \$u->role = 'guru'; \$u->save();                                   ║\n";
        echo "║                                                                   ║\n";
        echo "║  Switch ke role wali-kelas:                                       ║\n";
        echo "║  \$u->role = 'wali-kelas'; \$u->save();                             ║\n";
        echo "║                                                                   ║\n";
        echo "║  Switch ke role kepala-sekolah:                                   ║\n";
        echo "║  \$u->role = 'kepala-sekolah'; \$u->save();                         ║\n";
        echo "║                                                                   ║\n";
        echo "║  Switch ke role admin:                                            ║\n";
        echo "║  \$u->role = 'admin'; \$u->save();                                  ║\n";
        echo "╚═══════════════════════════════════════════════════════════════════╝\n";
        echo "\n";
    }
}
