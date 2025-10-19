<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder untuk membuat user dengan role tata-usaha, admin, dan super-admin
     * untuk keperluan testing dan development
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password123'),
                'role' => 'super-admin',
                'is_active' => true,
            ],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'Tata Usaha',
                'username' => 'tatausaha',
                'email' => 'tatausaha@example.com',
                'password' => Hash::make('password123'),
                'role' => 'tata-usaha',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            // Cek apakah user sudah ada
            $existingUser = DB::table('users')->where('email', $userData['email'])->first();
            
            if (!$existingUser) {
                DB::table('users')->insert([
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'email' => $userData['email'],
                    'password' => $userData['password'],
                    'role' => $userData['role'],
                    'is_active' => $userData['is_active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                echo "✓ Created {$userData['role']}: {$userData['email']}\n";
            } else {
                echo "• {$userData['role']} already exists: {$userData['email']}\n";
            }
        }

        echo "\n";
        echo "╔═══════════════════════════════════════════════════════════════════╗\n";
        echo "║          ✓ ADMIN USERS SEEDED SUCCESSFULLY!                      ║\n";
        echo "╠═══════════════════════════════════════════════════════════════════╣\n";
        echo "║  Super Admin                                                      ║\n";
        echo "║  Email    : superadmin@example.com                                ║\n";
        echo "║  Password : password123                                           ║\n";
        echo "║  Role     : super-admin                                           ║\n";
        echo "║  Can      : Create semua role kecuali super-admin                 ║\n";
        echo "║                                                                   ║\n";
        echo "║  Admin                                                            ║\n";
        echo "║  Email    : admin@example.com                                     ║\n";
        echo "║  Password : password123                                           ║\n";
        echo "║  Role     : admin                                                 ║\n";
        echo "║  Can      : Create semua role kecuali admin & super-admin         ║\n";
        echo "║                                                                   ║\n";
        echo "║  Tata Usaha                                                       ║\n";
        echo "║  Email    : tatausaha@example.com                                 ║\n";
        echo "║  Password : password123                                           ║\n";
        echo "║  Role     : tata-usaha                                            ║\n";
        echo "║  Can      : Create siswa, orang-tua, guru, wali-kelas             ║\n";
        echo "╠═══════════════════════════════════════════════════════════════════╣\n";
        echo "║  TESTING REGISTER:                                                ║\n";
        echo "║  1. Login dengan salah satu akun di atas                          ║\n";
        echo "║  2. POST /api/v1/auth/register dengan token                       ║\n";
        echo "║  3. Sesuaikan role yang dibuat dengan permission masing-masing    ║\n";
        echo "╚═══════════════════════════════════════════════════════════════════╝\n";
        echo "\n";
    }
}
