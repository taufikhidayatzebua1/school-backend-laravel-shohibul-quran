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
     * 
     * Best Practice:
     * - Semua guru, wali-kelas, dan kepala-sekolah disimpan di tabel 'guru'
     * - Role ditentukan di tabel 'users'
     * - Wali-kelas adalah guru yang juga mengajar, hanya berperan sebagai wali kelas
     * - Kepala sekolah juga memiliki data di tabel guru untuk konsistensi
     */
    public function run(): void
    {
        // Buat user untuk semua guru (termasuk wali-kelas dan kepala-sekolah)
        $users = [
            // Guru Biasa (5 orang)
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
            
            // Wali Kelas (5 orang) - Mereka juga guru, tapi punya role wali-kelas
            [
                'name' => 'Fitria Handayani',
                'email' => 'fitria.handayani@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'wali-kelas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gunawan Sukarno',
                'email' => 'gunawan.sukarno@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'wali-kelas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hani Wijayanti',
                'email' => 'hani.wijayanti@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'wali-kelas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Indra Kusuma',
                'email' => 'indra.kusuma@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'wali-kelas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Julia Permatasari',
                'email' => 'julia.permatasari@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'wali-kelas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Kepala Sekolah (1 orang) - Juga memiliki data di tabel guru
            [
                'name' => 'Dr. Agus Salim, M.Pd',
                'email' => 'kepala.sekolah@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'kepala-sekolah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            DB::table('users')->insert($userData);
        }

        // Ambil user_id yang baru dibuat
        $userIds = DB::table('users')
            ->whereIn('role', ['guru', 'wali-kelas', 'kepala-sekolah'])
            ->pluck('id', 'email');

        // Insert data guru untuk SEMUA (guru biasa, wali-kelas, kepala-sekolah)
        DB::table('guru')->insert([
            // Guru Biasa
            [
                'user_id' => $userIds['budi.santoso@sekolah.com'],
                'nip' => '197505102000031001',
                'nama' => 'Budi Santoso, S.Pd',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1975-05-10',
                'alamat' => 'Jl. Merdeka No. 10, Jakarta',
                'no_hp' => '081234567801',
                'url_photo' => 'https://ui-avatars.com/api/?name=Budi+Santoso&background=0D8ABC&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['siti.aminah@sekolah.com'],
                'nip' => '198203152005022001',
                'nama' => 'Siti Aminah, S.Pd',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1982-03-15',
                'alamat' => 'Jl. Sudirman No. 25, Jakarta',
                'no_hp' => '081234567802',
                'url_photo' => 'https://ui-avatars.com/api/?name=Siti+Aminah&background=FF69B4&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['ahmad.yani@sekolah.com'],
                'nip' => '198507202008011002',
                'nama' => 'Ahmad Yani, S.Pd',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1985-07-20',
                'alamat' => 'Jl. Gatot Subroto No. 15, Jakarta',
                'no_hp' => '081234567803',
                'url_photo' => 'https://ui-avatars.com/api/?name=Ahmad+Yani&background=28A745&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['dewi.lestari@sekolah.com'],
                'nip' => '199001052010122001',
                'nama' => 'Dewi Lestari, S.Pd',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1990-01-05',
                'alamat' => 'Jl. Ahmad Yani No. 30, Jakarta',
                'no_hp' => '081234567804',
                'url_photo' => 'https://ui-avatars.com/api/?name=Dewi+Lestari&background=9C27B0&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['eko.prasetyo@sekolah.com'],
                'nip' => '198812102012031001',
                'nama' => 'Eko Prasetyo, S.Pd',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1988-12-10',
                'alamat' => 'Jl. Diponegoro No. 20, Jakarta',
                'no_hp' => '081234567805',
                'url_photo' => 'https://ui-avatars.com/api/?name=Eko+Prasetyo&background=FFC107&color=000&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Wali Kelas (mereka juga guru dengan role berbeda)
            [
                'user_id' => $userIds['fitria.handayani@sekolah.com'],
                'nip' => '198605182009122001',
                'nama' => 'Fitria Handayani, S.Pd',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1986-05-18',
                'alamat' => 'Jl. Veteran No. 18, Jakarta',
                'no_hp' => '081234567806',
                'url_photo' => 'https://ui-avatars.com/api/?name=Fitria+Handayani&background=E91E63&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['gunawan.sukarno@sekolah.com'],
                'nip' => '197908152003121001',
                'nama' => 'Gunawan Sukarno, S.Pd',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Malang',
                'tanggal_lahir' => '1979-08-15',
                'alamat' => 'Jl. Pahlawan No. 22, Jakarta',
                'no_hp' => '081234567807',
                'url_photo' => 'https://ui-avatars.com/api/?name=Gunawan+Sukarno&background=3F51B5&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['hani.wijayanti@sekolah.com'],
                'nip' => '199203102015022001',
                'nama' => 'Hani Wijayanti, S.Pd',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Solo',
                'tanggal_lahir' => '1992-03-10',
                'alamat' => 'Jl. Pattimura No. 16, Jakarta',
                'no_hp' => '081234567808',
                'url_photo' => 'https://ui-avatars.com/api/?name=Hani+Wijayanti&background=00BCD4&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['indra.kusuma@sekolah.com'],
                'nip' => '198411252007011001',
                'nama' => 'Indra Kusuma, S.Pd',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Palembang',
                'tanggal_lahir' => '1984-11-25',
                'alamat' => 'Jl. Kartini No. 28, Jakarta',
                'no_hp' => '081234567809',
                'url_photo' => 'https://ui-avatars.com/api/?name=Indra+Kusuma&background=FF5722&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userIds['julia.permatasari@sekolah.com'],
                'nip' => '199107082014022001',
                'nama' => 'Julia Permatasari, S.Pd',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Makassar',
                'tanggal_lahir' => '1991-07-08',
                'alamat' => 'Jl. Hasanudin No. 12, Jakarta',
                'no_hp' => '081234567810',
                'url_photo' => 'https://ui-avatars.com/api/?name=Julia+Permatasari&background=4CAF50&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Kepala Sekolah (juga punya data di tabel guru)
            [
                'user_id' => $userIds['kepala.sekolah@sekolah.com'],
                'nip' => '196805151990031001',
                'nama' => 'Dr. Agus Salim, M.Pd',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1968-05-15',
                'alamat' => 'Jl. Pendidikan No. 1, Jakarta',
                'no_hp' => '081234567899',
                'url_photo' => 'https://ui-avatars.com/api/?name=Agus+Salim&background=8B0000&color=fff&size=200',
                'url_cover' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        echo "✓ Seeder berhasil!\n";
        echo "  - 5 Guru biasa\n";
        echo "  - 5 Wali Kelas (juga guru, role: wali-kelas)\n";
        echo "  - 1 Kepala Sekolah (juga guru, role: kepala-sekolah)\n";
        echo "  - Total: 11 data guru dengan berbagai role\n";
    }
}
