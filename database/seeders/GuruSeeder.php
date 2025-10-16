<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user untuk guru (10 guru)
        $users = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti.aminah@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ahmad Yani',
                'email' => 'ahmad.yani@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fitria Handayani',
                'email' => 'fitria.handayani@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gunawan Sukarno',
                'email' => 'gunawan.sukarno@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hani Wijayanti',
                'email' => 'hani.wijayanti@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Indra Kusuma',
                'email' => 'indra.kusuma@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Julia Permatasari',
                'email' => 'julia.permatasari@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            DB::table('users')->insert($userData);
        }

        // Ambil user_id yang baru dibuat
        $userIds = DB::table('users')
            ->where('role', 'guru')
            ->pluck('id', 'email');

        // Insert data guru
        DB::table('guru')->insert([
            [
                'user_id' => $userIds['budi.santoso@sekolah.com'],
                'nip' => '197505102000031001',
                'nama' => 'Budi Santoso, S.Pd',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1975-05-10',
                'alamat' => 'Jl. Merdeka No. 10, Jakarta',
                'no_hp' => '081234567801',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['siti.aminah@sekolah.com'],
                'nip' => '198203152005022001',
                'nama' => 'Siti Aminah, S.Pd',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1982-03-15',
                'alamat' => 'Jl. Sudirman No. 25, Jakarta',
                'no_hp' => '081234567802',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['ahmad.yani@sekolah.com'],
                'nip' => '198507202008011002',
                'nama' => 'Ahmad Yani, S.Pd',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1985-07-20',
                'alamat' => 'Jl. Gatot Subroto No. 15, Jakarta',
                'no_hp' => '081234567803',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['dewi.lestari@sekolah.com'],
                'nip' => '199001052010122001',
                'nama' => 'Dewi Lestari, S.Pd',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1990-01-05',
                'alamat' => 'Jl. Ahmad Yani No. 30, Jakarta',
                'no_hp' => '081234567804',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['eko.prasetyo@sekolah.com'],
                'nip' => '198812102012031001',
                'nama' => 'Eko Prasetyo, S.Pd',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1988-12-10',
                'alamat' => 'Jl. Diponegoro No. 20, Jakarta',
                'no_hp' => '081234567805',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['fitria.handayani@sekolah.com'],
                'nip' => '198605182009122001',
                'nama' => 'Fitria Handayani, S.Pd',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1986-05-18',
                'alamat' => 'Jl. Veteran No. 18, Jakarta',
                'no_hp' => '081234567806',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['gunawan.sukarno@sekolah.com'],
                'nip' => '197908152003121001',
                'nama' => 'Gunawan Sukarno, S.Pd',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1979-08-15',
                'alamat' => 'Jl. Pahlawan No. 22, Jakarta',
                'no_hp' => '081234567807',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['hani.wijayanti@sekolah.com'],
                'nip' => '199203102015022001',
                'nama' => 'Hani Wijayanti, S.Pd',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1992-03-10',
                'alamat' => 'Jl. Pattimura No. 16, Jakarta',
                'no_hp' => '081234567808',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['indra.kusuma@sekolah.com'],
                'nip' => '198411252007011001',
                'nama' => 'Indra Kusuma, S.Pd',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1984-11-25',
                'alamat' => 'Jl. Kartini No. 28, Jakarta',
                'no_hp' => '081234567809',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['julia.permatasari@sekolah.com'],
                'nip' => '199107082014022001',
                'nama' => 'Julia Permatasari, S.Pd',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1991-07-08',
                'alamat' => 'Jl. Hasanudin No. 12, Jakarta',
                'no_hp' => '081234567810',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
