<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OrangTuaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Best Practice:
     * - Semua orang tua disimpan di tabel 'users' dengan role 'orang-tua'
     * - Data lengkap orang tua disimpan di tabel 'orang_tua'
     * - Satu orang tua bisa memiliki beberapa siswa (relasi one-to-many)
     */
    public function run(): void
    {
        // Buat user untuk orang tua
        $users = [
            [
                'name' => 'Bambang Wijaya',
                'username' => 'bambang_wijaya',
                'email' => 'bambang.wijaya@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Siti Rahmawati',
                'username' => 'siti_rahmawati',
                'email' => 'siti.rahmawati@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hadi Setiawan',
                'username' => 'hadi_setiawan',
                'email' => 'hadi.setiawan@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dewi Anggraini',
                'username' => 'dewi_anggraini',
                'email' => 'dewi.anggraini@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Agus Kurniawan',
                'username' => 'agus_kurniawan',
                'email' => 'agus.kurniawan@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rina Marlina',
                'username' => 'rina_marlina',
                'email' => 'rina.marlina@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Supriyadi',
                'username' => 'supriyadi',
                'email' => 'supriyadi@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nurul Hidayah',
                'username' => 'nurul_hidayah',
                'email' => 'nurul.hidayah@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ridwan Kamil',
                'username' => 'ridwan_kamil',
                'email' => 'ridwan.kamil@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'orang-tua',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ani Yudhoyono',
                'username' => 'ani_yudhoyono',
                'email' => 'ani.yudhoyono@parent.com',
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

        // Ambil user_id yang baru dibuat
        $userIds = DB::table('users')
            ->where('role', 'orang-tua')
            ->whereIn('email', [
                'bambang.wijaya@parent.com',
                'siti.rahmawati@parent.com',
                'hadi.setiawan@parent.com',
                'dewi.anggraini@parent.com',
                'agus.kurniawan@parent.com',
                'rina.marlina@parent.com',
                'supriyadi@parent.com',
                'nurul.hidayah@parent.com',
                'ridwan.kamil@parent.com',
                'ani.yudhoyono@parent.com',
            ])
            ->pluck('id', 'email');

        // Insert data lengkap orang tua
        DB::table('orang_tua')->insert([
            // Ayah dari siswa Andi Wijaya
            [
                'user_id' => $userIds['bambang.wijaya@parent.com'],
                'nama' => 'Bambang Wijaya',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1980-03-15',
                'alamat' => 'Jl. Kenanga No. 10, Jakarta Selatan',
                'no_hp' => '081234567890',
                'pendidikan' => 'S1 Teknik',
                'pekerjaan' => 'Pegawai Swasta',
                'penghasilan' => 8500000.00,
                'url_photo' => 'https://ui-avatars.com/api/?name=Bambang+Wijaya&background=0D8ABC&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ibu dari siswa Siti Nurhaliza
            [
                'user_id' => $userIds['siti.rahmawati@parent.com'],
                'nama' => 'Siti Rahmawati',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1982-05-20',
                'alamat' => 'Jl. Melati No. 15, Jakarta Timur',
                'no_hp' => '081234567891',
                'pendidikan' => 'S1 Pendidikan',
                'pekerjaan' => 'Guru',
                'penghasilan' => 6500000.00,
                'url_photo' => 'https://ui-avatars.com/api/?name=Siti+Rahmawati&background=FF69B4&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ayah dari siswa Budi Setiawan
            [
                'user_id' => $userIds['hadi.setiawan@parent.com'],
                'nama' => 'Hadi Setiawan',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1978-08-10',
                'alamat' => 'Jl. Mawar No. 20, Jakarta Pusat',
                'no_hp' => '081234567892',
                'pendidikan' => 'S2 Manajemen',
                'pekerjaan' => 'Manajer',
                'penghasilan' => 15000000.00,
                'url_photo' => 'https://ui-avatars.com/api/?name=Hadi+Setiawan&background=28A745&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ibu dari siswa Rina Anggraini
            [
                'user_id' => $userIds['dewi.anggraini@parent.com'],
                'nama' => 'Dewi Anggraini',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1985-11-05',
                'alamat' => 'Jl. Anggrek No. 5, Jakarta Barat',
                'no_hp' => '081234567893',
                'pendidikan' => 'S1 Ekonomi',
                'pekerjaan' => 'Akuntan',
                'penghasilan' => 7500000.00,
                'url_photo' => 'https://ui-avatars.com/api/?name=Dewi+Anggraini&background=9C27B0&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ayah dari siswa Dedi Kurniawan
            [
                'user_id' => $userIds['agus.kurniawan@parent.com'],
                'nama' => 'Agus Kurniawan',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1979-02-18',
                'alamat' => 'Jl. Dahlia No. 8, Jakarta Utara',
                'no_hp' => '081234567894',
                'pendidikan' => 'S1 Hukum',
                'pekerjaan' => 'Pengacara',
                'penghasilan' => 12000000.00,
                'url_photo' => 'https://ui-avatars.com/api/?name=Agus+Kurniawan&background=FFC107&color=000&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ibu dari siswa Maya Sari
            [
                'user_id' => $userIds['rina.marlina@parent.com'],
                'nama' => 'Rina Marlina',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1983-07-25',
                'alamat' => 'Jl. Tulip No. 12, Jakarta Selatan',
                'no_hp' => '081234567895',
                'pendidikan' => 'D3 Kesehatan',
                'pekerjaan' => 'Perawat',
                'penghasilan' => 5500000.00,
                'url_photo' => 'https://ui-avatars.com/api/?name=Rina+Marlina&background=E91E63&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ayah dari siswa Fajar Ramadhan
            [
                'user_id' => $userIds['supriyadi@parent.com'],
                'nama' => 'Supriyadi',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Palembang',
                'tanggal_lahir' => '1981-04-12',
                'alamat' => 'Jl. Sakura No. 7, Jakarta Timur',
                'no_hp' => '081234567896',
                'pendidikan' => 'S1 Teknik Sipil',
                'pekerjaan' => 'Kontraktor',
                'penghasilan' => 18000000.00,
                'url_photo' => 'https://ui-avatars.com/api/?name=Supriyadi&background=3F51B5&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ibu dari siswa Lina Marlina
            [
                'user_id' => $userIds['nurul.hidayah@parent.com'],
                'nama' => 'Nurul Hidayah',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Makassar',
                'tanggal_lahir' => '1984-09-30',
                'alamat' => 'Jl. Flamboyan No. 14, Jakarta Pusat',
                'no_hp' => '081234567897',
                'pendidikan' => 'S1 Psikologi',
                'pekerjaan' => 'Psikolog',
                'penghasilan' => 9500000.00,
                'url_photo' => 'https://ui-avatars.com/api/?name=Nurul+Hidayah&background=00BCD4&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ayah dari siswa Hendra Gunawan
            [
                'user_id' => $userIds['ridwan.kamil@parent.com'],
                'nama' => 'Ridwan Kamil',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bali',
                'tanggal_lahir' => '1977-12-22',
                'alamat' => 'Jl. Cempaka No. 9, Jakarta Barat',
                'no_hp' => '081234567898',
                'pendidikan' => 'S2 Arsitektur',
                'pekerjaan' => 'Arsitek',
                'penghasilan' => 22000000.00,
                'url_photo' => 'https://ui-avatars.com/api/?name=Ridwan+Kamil&background=FF5722&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ibu dari siswa Putri Ayu
            [
                'user_id' => $userIds['ani.yudhoyono@parent.com'],
                'nama' => 'Ani Yudhoyono',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Pontianak',
                'tanggal_lahir' => '1980-06-14',
                'alamat' => 'Jl. Orchid No. 11, Jakarta Utara',
                'no_hp' => '081234567899',
                'pendidikan' => 'S1 Komunikasi',
                'pekerjaan' => 'Public Relations',
                'penghasilan' => 10500000.00,
                'url_photo' => 'https://ui-avatars.com/api/?name=Ani+Yudhoyono&background=4CAF50&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        echo "✓ OrangTuaSeeder berhasil!\n";
        echo "  - 10 Orang Tua dengan data lengkap\n";
        echo "  - Data meliputi: nama, jenis kelamin, TTL, alamat, no HP,\n";
        echo "    pendidikan, pekerjaan, penghasilan, dan foto profil\n";
        echo "  - Semua user memiliki role 'orang-tua'\n";
    }
}
