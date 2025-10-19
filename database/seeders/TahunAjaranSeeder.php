<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tahun_ajaran')->insert([
            // Format standar: Ganjil/Genap
            [
                'semester' => 'Ganjil',
                'tahun' => '2023/2024',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'semester' => 'Genap',
                'tahun' => '2023/2024',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Tahun ajaran aktif
            [
                'semester' => 'Ganjil',
                'tahun' => '2024/2025',
                'is_active' => true, // Tahun ajaran aktif saat ini
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'semester' => 'Genap',
                'tahun' => '2024/2025',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Tahun ajaran mendatang
            [
                'semester' => 'Ganjil',
                'tahun' => '2025/2026',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Contoh format alternatif yang juga valid:
            // Uncomment jika ingin menggunakan format berbeda
            
            // [
            //     'semester' => 'Semester 1',
            //     'tahun' => '2025/2026',
            //     'is_active' => false,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'semester' => 'Quarter 1',
            //     'tahun' => '2025',
            //     'is_active' => false,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'semester' => 'Term 1',
            //     'tahun' => '2025/2026',
            //     'is_active' => false,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ]);
    }
}
