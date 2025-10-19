<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * 
     * Urutan seeder yang benar:
     * 1. OtherRolesSeeder - Membuat admin, super-admin, tata-usaha, yayasan (TANPA orang-tua)
     * 2. GuruSeeder - Membuat guru, wali-kelas, kepala-sekolah (semua masuk tabel guru)
     * 3. TahunAjaranSeeder - Membuat tahun ajaran (bergantung pada GuruSeeder)
     * 4. KelasSeeder - Membuat kelas (bergantung pada guru untuk wali_kelas_id dan tahun_ajaran)
     * 5. SiswaSeeder - Membuat siswa (bergantung pada kelas)
     * 6. OrangTuaSeeder - Membuat orang tua dengan data lengkap (bergantung pada users)
     * 7. HafalanSeeder - Membuat hafalan (bergantung pada siswa dan guru)
     * 8. TestingUserSeeder - Membuat user testing khusus (ada di siswa & guru) - OPSIONAL
     * 
     * Note: 
     * - AdminUserSeeder TIDAK dipakai karena duplikat dengan OtherRolesSeeder
     * - OrangTuaSeeder terpisah dengan data lengkap di tabel orang_tua
     */
    public function run(): void
    {
        $this->call([
            OtherRolesSeeder::class,        // Admin, super-admin, tata-usaha, yayasan
            GuruSeeder::class,              // Guru, wali-kelas, kepala-sekolah
            TahunAjaranSeeder::class,       // Tahun ajaran
            KelasSeeder::class,             // Kelas
            SiswaSeeder::class,             // Siswa
            OrangTuaSeeder::class,          // Orang tua (lengkap dengan tabel orang_tua)
            HafalanSeeder::class,           // Hafalan
            TestingUserSeeder::class,       // Uncomment jika perlu user testing
        ]);
    }
}
