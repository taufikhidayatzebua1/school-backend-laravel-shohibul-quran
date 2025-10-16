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
     */
    public function run(): void
    {
        // Buat user untuk role lainnya
        $users = [
            // Wali Kelas
            [
                'name' => 'Wali Kelas 1A',
                'email' => 'wali.kelas1a@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'wali_kelas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Super Admin
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'super-admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Admin
            [
                'name' => 'Admin Sistem',
                'email' => 'admin@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Kepala Sekolah
            [
                'name' => 'Dr. Agus Salim, M.Pd',
                'email' => 'kepala.sekolah@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'kepala-sekolah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tata Usaha
            [
                'name' => 'Rina Kartika',
                'email' => 'tata.usaha1@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'tata-usaha',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bambang Supriyanto',
                'email' => 'tata.usaha2@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'tata-usaha',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Yayasan
            [
                'name' => 'H. Abdul Rahman',
                'email' => 'yayasan1@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'yayasan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hj. Siti Fatimah',
                'email' => 'yayasan2@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'yayasan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Orang Tua (parents of students)
            [
                'name' => 'Bambang Wijaya (Ayah Andi)',
                'email' => 'orangtua.andi@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sri Nurhaliza (Ibu Siti)',
                'email' => 'orangtua.siti@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hadi Setiawan (Ayah Budi)',
                'email' => 'orangtua.budi@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dewi Anggraini (Ibu Rina)',
                'email' => 'orangtua.rina@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Agus Kurniawan (Ayah Dedi)',
                'email' => 'orangtua.dedi@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            DB::table('users')->insert($userData);
        }
    }
}
