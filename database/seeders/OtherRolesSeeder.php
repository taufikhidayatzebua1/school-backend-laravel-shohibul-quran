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
     * Seeder ini hanya untuk role yang BUKAN guru/wali-kelas/kepala-sekolah:
     * - super-admin
     * - admin
     * - tata-usaha
     * - yayasan
     * - orang-tua
     * 
     * Note: Guru, wali-kelas, dan kepala-sekolah ada di GuruSeeder
     */
    public function run(): void
    {
        // Buat user untuk role lainnya
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
                'username' => 'admin_sistem',
                'email' => 'admin@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tata Usaha
            [
                'name' => 'Rina Kartika',
                'username' => 'rina_kartika',
                'email' => 'tata.usaha1@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'tata-usaha',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bambang Supriyanto',
                'username' => 'bambang_supriyanto',
                'email' => 'tata.usaha2@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'tata-usaha',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Yayasan
            [
                'name' => 'H. Abdul Rahman',
                'username' => 'abdul_rahman',
                'email' => 'yayasan1@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'yayasan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hj. Siti Fatimah',
                'username' => 'siti_fatimah',
                'email' => 'yayasan2@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'yayasan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Orang Tua (parents of students)
            [
                'name' => 'Bambang Wijaya (Ayah Andi)',
                'username' => 'bambang_wijaya',
                'email' => 'orangtua.andi@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sri Nurhaliza (Ibu Siti)',
                'username' => 'sri_nurhaliza',
                'email' => 'orangtua.siti@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hadi Setiawan (Ayah Budi)',
                'username' => 'hadi_setiawan',
                'email' => 'orangtua.budi@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dewi Anggraini (Ibu Rina)',
                'username' => 'dewi_anggraini',
                'email' => 'orangtua.rina@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Agus Kurniawan (Ayah Dedi)',
                'username' => 'agus_kurniawan',
                'email' => 'orangtua.dedi@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            DB::table('users')->insert($userData);
        }

        echo "✓ OtherRolesSeeder berhasil!\n";
        echo "  - 1 Super Admin\n";
        echo "  - 1 Admin\n";
        echo "  - 2 Tata Usaha\n";
        echo "  - 2 Yayasan\n";
        echo "  - 5 Orang Tua\n";
        echo "  - Total: 11 user non-guru\n";
    }
}
