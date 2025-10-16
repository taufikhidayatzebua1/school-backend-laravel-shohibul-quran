<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user untuk siswa (10 siswa)
        $users = [
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi.wijaya@siswa.com',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@siswa.com',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Budi Setiawan',
                'email' => 'budi.setiawan@siswa.com',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rina Anggraini',
                'email' => 'rina.anggraini@siswa.com',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dedi Kurniawan',
                'email' => 'dedi.kurniawan@siswa.com',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maya Sari',
                'email' => 'maya.sari@siswa.com',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fajar Ramadhan',
                'email' => 'fajar.ramadhan@siswa.com',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lina Marlina',
                'email' => 'lina.marlina@siswa.com',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hendra Gunawan',
                'email' => 'hendra.gunawan@siswa.com',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Putri Ayu',
                'email' => 'putri.ayu@siswa.com',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            DB::table('users')->insert($userData);
        }

        // Ambil user_id yang baru dibuat
        $userIds = DB::table('users')
            ->where('role', 'siswa')
            ->pluck('id', 'email');

        // Ambil ID kelas
        $kelasIds = DB::table('kelas')->pluck('id');

        // Insert data siswa
        DB::table('siswa')->insert([
            [
                'user_id' => $userIds['andi.wijaya@siswa.com'],
                'nis' => '2024001',
                'nama' => 'Andi Wijaya',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2008-05-15',
                'alamat' => 'Jl. Kenanga No. 10, Jakarta',
                'kelas_id' => $kelasIds[0] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['siti.nurhaliza@siswa.com'],
                'nis' => '2024002',
                'nama' => 'Siti Nurhaliza',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2008-08-20',
                'alamat' => 'Jl. Melati No. 15, Jakarta',
                'kelas_id' => $kelasIds[0] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['budi.setiawan@siswa.com'],
                'nis' => '2024003',
                'nama' => 'Budi Setiawan',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2008-03-10',
                'alamat' => 'Jl. Mawar No. 20, Jakarta',
                'kelas_id' => $kelasIds[1] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['rina.anggraini@siswa.com'],
                'nis' => '2024004',
                'nama' => 'Rina Anggraini',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2008-11-25',
                'alamat' => 'Jl. Anggrek No. 5, Jakarta',
                'kelas_id' => $kelasIds[1] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['dedi.kurniawan@siswa.com'],
                'nis' => '2023001',
                'nama' => 'Dedi Kurniawan',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2007-06-12',
                'alamat' => 'Jl. Dahlia No. 8, Jakarta',
                'kelas_id' => $kelasIds[2] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['maya.sari@siswa.com'],
                'nis' => '2023002',
                'nama' => 'Maya Sari',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2007-09-18',
                'alamat' => 'Jl. Tulip No. 12, Jakarta',
                'kelas_id' => $kelasIds[2] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['fajar.ramadhan@siswa.com'],
                'nis' => '2023003',
                'nama' => 'Fajar Ramadhan',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2007-04-22',
                'alamat' => 'Jl. Sakura No. 7, Jakarta',
                'kelas_id' => $kelasIds[3] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['lina.marlina@siswa.com'],
                'nis' => '2023004',
                'nama' => 'Lina Marlina',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2007-12-08',
                'alamat' => 'Jl. Flamboyan No. 14, Jakarta',
                'kelas_id' => $kelasIds[3] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['hendra.gunawan@siswa.com'],
                'nis' => '2022001',
                'nama' => 'Hendra Gunawan',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2006-07-30',
                'alamat' => 'Jl. Cempaka No. 9, Jakarta',
                'kelas_id' => $kelasIds[4] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['putri.ayu@siswa.com'],
                'nis' => '2022002',
                'nama' => 'Putri Ayu',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2006-02-14',
                'alamat' => 'Jl. Teratai No. 11, Jakarta',
                'kelas_id' => $kelasIds[4] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
