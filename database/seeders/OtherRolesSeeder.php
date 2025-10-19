<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OtherRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder ini hanya untuk role yang BUKAN guru/wali-kelas/kepala-sekolah/orang-tua/siswa:
     * - super-admin (1 orang)
     * - admin (1 orang)
     * - tata-usaha (1 orang, cukup untuk testing)
     * - yayasan (1 orang, cukup untuk testing)
     * 
     * Note: 
     * - Guru, wali-kelas, dan kepala-sekolah ada di GuruSeeder
     * - Orang-tua ada di OrangTuaSeeder (dengan data lengkap di tabel orang_tua)
     * - Siswa ada di SiswaSeeder
     * - AdminUserSeeder TIDAK DIGUNAKAN (duplikat dengan seeder ini)
     */
    public function run(): void
    {
        // Buat user untuk role lainnya (sederhana, cukup 1 per role untuk testing)
        $users = [
            // Super Admin
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'superadmin@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'super-admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Admin
            [
                'name' => 'Admin Sistem',
                'username' => 'admin',
                'email' => 'admin@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tata Usaha (cukup 1 untuk testing)
            [
                'name' => 'Tata Usaha',
                'username' => 'tatausaha',
                'email' => 'tatausaha@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'tata-usaha',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Yayasan (cukup 1 untuk testing)
            [
                'name' => 'Yayasan',
                'username' => 'yayasan',
                'email' => 'yayasan@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'yayasan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            // Check if user already exists to avoid duplicate
            $exists = DB::table('users')->where('email', $userData['email'])->exists();
            
            if (!$exists) {
                DB::table('users')->insert($userData);
                echo "✓ Created: {$userData['role']} - {$userData['email']}\n";
            } else {
                echo "• Already exists: {$userData['email']}\n";
            }
        }

        echo "\n";
        echo "✓ OtherRolesSeeder berhasil!\n";
        echo "  - 1 Super Admin (superadmin@sekolah.com)\n";
        echo "  - 1 Admin (admin@sekolah.com)\n";
        echo "  - 1 Tata Usaha (tatausaha@sekolah.com)\n";
        echo "  - 1 Yayasan (yayasan@sekolah.com)\n";
        echo "  - Password semua: password123\n";
        echo "  - Total: 4 user administrasi\n";
        echo "\n";
        echo "  Note: Orang tua ada di OrangTuaSeeder dengan data lengkap\n";
    }
}
