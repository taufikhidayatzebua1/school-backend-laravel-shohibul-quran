<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KepalaSekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create user for kepala sekolah
        $userId = DB::table('users')->insertGetId([
            'name' => 'Taufik Hizet',
            'email' => 'taufikhizet1350@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'kepala-sekolah',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "✓ User kepala sekolah berhasil dibuat:\n";
        echo "  - ID: $userId\n";
        echo "  - Name: Taufik Hizet\n";
        echo "  - Email: taufikhizet1350@gmail.com\n";
        echo "  - Password: password123\n";
        echo "  - Role: kepala-sekolah\n";
    }
}
