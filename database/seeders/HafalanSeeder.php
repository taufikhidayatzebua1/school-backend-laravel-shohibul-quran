<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HafalanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID siswa dan guru yang ada
        $siswaIds = DB::table('siswa')->pluck('id')->toArray();
        $guruIds = DB::table('guru')->pluck('id')->toArray();

        // Daftar surah dalam Al-Quran dengan jumlah ayat (30 Juz terakhir - Juz Amma)
        // Surah 1 = Al-Fatihah, 78-114 = Juz Amma
        $surahData = [
            1 => ['nama' => 'Al-Fatihah', 'ayat' => 7],
            78 => ['nama' => 'An-Naba', 'ayat' => 40],
            79 => ['nama' => 'An-Naziat', 'ayat' => 46],
            80 => ['nama' => 'Abasa', 'ayat' => 42],
            81 => ['nama' => 'At-Takwir', 'ayat' => 29],
            82 => ['nama' => 'Al-Infitar', 'ayat' => 19],
            83 => ['nama' => 'Al-Mutaffifin', 'ayat' => 36],
            84 => ['nama' => 'Al-Insyiqaq', 'ayat' => 25],
            85 => ['nama' => 'Al-Buruj', 'ayat' => 22],
            86 => ['nama' => 'At-Tariq', 'ayat' => 17],
            87 => ['nama' => 'Al-A\'la', 'ayat' => 19],
            88 => ['nama' => 'Al-Ghasyiyah', 'ayat' => 26],
            89 => ['nama' => 'Al-Fajr', 'ayat' => 30],
            90 => ['nama' => 'Al-Balad', 'ayat' => 20],
            91 => ['nama' => 'Asy-Syams', 'ayat' => 15],
            92 => ['nama' => 'Al-Lail', 'ayat' => 21],
            93 => ['nama' => 'Ad-Dhuha', 'ayat' => 11],
            94 => ['nama' => 'Asy-Syarh', 'ayat' => 8],
            95 => ['nama' => 'At-Tin', 'ayat' => 8],
            96 => ['nama' => 'Al-Alaq', 'ayat' => 19],
            97 => ['nama' => 'Al-Qadr', 'ayat' => 5],
            98 => ['nama' => 'Al-Bayyinah', 'ayat' => 8],
            99 => ['nama' => 'Az-Zalzalah', 'ayat' => 8],
            100 => ['nama' => 'Al-Adiyat', 'ayat' => 11],
            101 => ['nama' => 'Al-Qari\'ah', 'ayat' => 11],
            102 => ['nama' => 'At-Takasur', 'ayat' => 8],
            103 => ['nama' => 'Al-Asr', 'ayat' => 3],
            104 => ['nama' => 'Al-Humazah', 'ayat' => 9],
            105 => ['nama' => 'Al-Fil', 'ayat' => 5],
            106 => ['nama' => 'Quraisy', 'ayat' => 4],
            107 => ['nama' => 'Al-Ma\'un', 'ayat' => 7],
            108 => ['nama' => 'Al-Kausar', 'ayat' => 3],
            109 => ['nama' => 'Al-Kafirun', 'ayat' => 6],
            110 => ['nama' => 'An-Nasr', 'ayat' => 3],
            111 => ['nama' => 'Al-Lahab', 'ayat' => 5],
            112 => ['nama' => 'Al-Ikhlas', 'ayat' => 4],
            113 => ['nama' => 'Al-Falaq', 'ayat' => 5],
            114 => ['nama' => 'An-Nas', 'ayat' => 6],
        ];

        $statusOptions = ['lancar', 'perlu_bimbingan', 'mengulang'];
        $hafalanData = [];

        // Generate hafalan untuk setiap siswa (5-10 hafalan per siswa)
        foreach ($siswaIds as $siswaId) {
            $jumlahHafalan = rand(5, 10);
            $surahKeys = array_keys($surahData);
            
            for ($i = 0; $i < $jumlahHafalan; $i++) {
                $surahId = $surahKeys[array_rand($surahKeys)];
                $totalAyat = $surahData[$surahId]['ayat'];
                
                // Random range ayat
                $ayatDari = rand(1, max(1, $totalAyat - 5));
                $ayatSampai = min($totalAyat, $ayatDari + rand(1, 10));
                
                // Random tanggal dalam 3 bulan terakhir
                $tanggal = Carbon::now()->subDays(rand(0, 90))->format('Y-m-d');
                
                // Random guru pembimbing
                $guruId = $guruIds[array_rand($guruIds)];
                
                // Random status
                $status = $statusOptions[array_rand($statusOptions)];
                
                // Generate keterangan berdasarkan status
                $keterangan = '';
                switch ($status) {
                    case 'lancar':
                        $keterangan = 'Hafalan sangat baik, bacaan tartil dan makhraj jelas. Lanjutkan!';
                        break;
                    case 'perlu_bimbingan':
                        $keterangan = 'Hafalan cukup baik, namun perlu perbaikan pada makhraj beberapa huruf.';
                        break;
                    case 'mengulang':
                        $keterangan = 'Hafalan masih terbata-bata, perlu mengulang beberapa kali lagi.';
                        break;
                }
                
                $hafalanData[] = [
                    'siswa_id' => $siswaId,
                    'guru_id' => $guruId,
                    'surah_id' => $surahId,
                    'ayat_dari' => $ayatDari,
                    'ayat_sampai' => $ayatSampai,
                    'status' => $status,
                    'tanggal' => $tanggal,
                    'keterangan' => $keterangan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert data hafalan
        DB::table('hafalan')->insert($hafalanData);

        // Output info
        echo "✓ Berhasil membuat " . count($hafalanData) . " data hafalan\n";
        echo "✓ Untuk " . count($siswaIds) . " siswa\n";
        echo "✓ Dengan " . count($guruIds) . " guru pembimbing\n";
    }
}
