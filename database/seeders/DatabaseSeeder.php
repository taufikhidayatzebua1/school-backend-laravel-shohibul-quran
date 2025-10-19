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
     * 1. GuruSeeder - Membuat guru, wali-kelas, kepala-sekolah (semua masuk tabel guru)
     * 2. OtherRolesSeeder - Membuat admin, super-admin, tata-usaha, yayasan, orang-tua
     * 3. KelasSeeder - Membuat kelas (bergantung pada guru untuk wali_kelas_id)
     * 4. SiswaSeeder - Membuat siswa (bergantung pada kelas)
     * 5. HafalanSeeder - Membuat hafalan (bergantung pada siswa dan guru)
     * 6. TestingUserSeeder - Membuat user testing khusus (ada di siswa & guru)
     */
    public function run(): void
    {
        $this->call([
            GuruSeeder::class,
            OtherRolesSeeder::class,
            KelasSeeder::class,
            SiswaSeeder::class,
            HafalanSeeder::class,
            TestingUserSeeder::class, // User testing khusus (last)
        ]);
    }
}
