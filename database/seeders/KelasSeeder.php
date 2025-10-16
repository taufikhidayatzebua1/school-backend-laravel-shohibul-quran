<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID guru untuk wali kelas
        $guruIds = DB::table('guru')->pluck('id');

        DB::table('kelas')->insert([
            [
                'nama_kelas' => 'X IPA 1',
                'wali_kelas_id' => $guruIds[0] ?? null,
                'tahun_ajaran' => '2024/2025',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'X IPA 2',
                'wali_kelas_id' => $guruIds[1] ?? null,
                'tahun_ajaran' => '2024/2025',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'XI IPA 1',
                'wali_kelas_id' => $guruIds[2] ?? null,
                'tahun_ajaran' => '2024/2025',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'XI IPA 2',
                'wali_kelas_id' => $guruIds[3] ?? null,
                'tahun_ajaran' => '2024/2025',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'XII IPA 1',
                'wali_kelas_id' => $guruIds[4] ?? null,
                'tahun_ajaran' => '2024/2025',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
