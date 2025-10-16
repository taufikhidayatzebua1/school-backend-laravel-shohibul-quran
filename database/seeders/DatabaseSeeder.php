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
     */
    public function run(): void
    {
        // Jalankan seeder secara berurutan
        // OtherRolesSeeder pertama untuk membuat user dengan role lain
        // GuruSeeder untuk membuat guru dan user dengan role guru
        // KelasSeeder untuk membuat kelas (depends on guru)
        // SiswaSeeder untuk membuat siswa dan user dengan role siswa (depends on kelas)
        // HafalanSeeder untuk membuat data hafalan (depends on siswa dan guru)
        $this->call([
            OtherRolesSeeder::class,
            GuruSeeder::class,
            KelasSeeder::class,
            SiswaSeeder::class,
            HafalanSeeder::class,
        ]);
    }
}
