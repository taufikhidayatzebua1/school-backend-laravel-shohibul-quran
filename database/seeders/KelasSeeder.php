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
        
        // Ambil tahun ajaran yang aktif
        $tahunAjaranAktif = DB::table('tahun_ajaran')->where('is_active', true)->first();

        DB::table('kelas')->insert([
            [
                'nama' => 'X IPA 1',
                'ruangan' => 'R101',
                'wali_kelas_id' => $guruIds[0] ?? null,
                'tahun_ajaran_id' => $tahunAjaranAktif?->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'X IPA 2',
                'ruangan' => 'R102',
                'wali_kelas_id' => $guruIds[1] ?? null,
                'tahun_ajaran_id' => $tahunAjaranAktif?->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'XI IPA 1',
                'ruangan' => 'R201',
                'wali_kelas_id' => $guruIds[2] ?? null,
                'tahun_ajaran_id' => $tahunAjaranAktif?->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'XI IPA 2',
                'ruangan' => 'R202',
                'wali_kelas_id' => $guruIds[3] ?? null,
                'tahun_ajaran_id' => $tahunAjaranAktif?->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'XII IPA 1',
                'ruangan' => 'R301',
                'wali_kelas_id' => $guruIds[4] ?? null,
                'tahun_ajaran_id' => $tahunAjaranAktif?->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
