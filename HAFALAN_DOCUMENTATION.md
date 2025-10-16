# Dokumentasi Tabel Hafalan

## Struktur Tabel

### Tabel: `hafalan`

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | ID unik hafalan |
| `siswa_id` | BIGINT UNSIGNED | FOREIGN KEY (siswa), NULLABLE, ON DELETE SET NULL | ID siswa yang menghafal |
| `guru_id` | BIGINT UNSIGNED | FOREIGN KEY (guru), NULLABLE, ON DELETE SET NULL | ID guru pembimbing |
| `surah_id` | INTEGER | NOT NULL | Nomor surah (1-114) |
| `ayat_dari` | INTEGER | NOT NULL | Ayat mulai hafalan |
| `ayat_sampai` | INTEGER | NOT NULL | Ayat akhir hafalan |
| `status` | ENUM | NOT NULL, DEFAULT 'perlu bimbingan' | Status hafalan: 'lancar', 'perlu bimbingan', 'mengulang' |
| `tanggal` | DATE | NOT NULL | Tanggal ujian/setoran hafalan |
| `keterangan` | TEXT | NULLABLE | Catatan dari guru pembimbing |
| `created_at` | TIMESTAMP | NULLABLE | Waktu record dibuat |
| `updated_at` | TIMESTAMP | NULLABLE | Waktu record terakhir diupdate |

## Relasi

- **siswa_id** → `siswa.id` (Many-to-One)
  - Setiap hafalan dimiliki oleh satu siswa
  - Satu siswa dapat memiliki banyak hafalan
  - ON DELETE SET NULL: Jika siswa dihapus, siswa_id menjadi NULL

- **guru_id** → `guru.id` (Many-to-One)
  - Setiap hafalan dibimbing oleh satu guru
  - Satu guru dapat membimbing banyak hafalan
  - ON DELETE SET NULL: Jika guru dihapus, guru_id menjadi NULL

## Enum Values

### Status Hafalan
1. **lancar** - Hafalan sangat baik, bacaan tartil dan makhraj jelas
2. **perlu bimbingan** - Hafalan cukup baik, namun perlu perbaikan
3. **mengulang** - Hafalan masih terbata-bata, perlu mengulang

## Data Seeder

### HafalanSeeder
- Membuat 5-10 hafalan untuk setiap siswa
- Total: ~74 hafalan untuk 10 siswa
- Menggunakan surah dari Juz Amma (Surah 78-114) dan Al-Fatihah (Surah 1)
- Tanggal random dalam 3 bulan terakhir
- Status random dengan distribusi merata
- Guru pembimbing dipilih secara random dari 10 guru yang ada

### Daftar Surah yang Digunakan

| No. Surah | Nama Surah | Jumlah Ayat |
|-----------|------------|-------------|
| 1 | Al-Fatihah | 7 |
| 78 | An-Naba | 40 |
| 79 | An-Naziat | 46 |
| 80 | Abasa | 42 |
| 81 | At-Takwir | 29 |
| 82 | Al-Infitar | 19 |
| 83 | Al-Mutaffifin | 36 |
| 84 | Al-Insyiqaq | 25 |
| 85 | Al-Buruj | 22 |
| 86 | At-Tariq | 17 |
| 87 | Al-A'la | 19 |
| 88 | Al-Ghasyiyah | 26 |
| 89 | Al-Fajr | 30 |
| 90 | Al-Balad | 20 |
| 91 | Asy-Syams | 15 |
| 92 | Al-Lail | 21 |
| 93 | Ad-Dhuha | 11 |
| 94 | Asy-Syarh | 8 |
| 95 | At-Tin | 8 |
| 96 | Al-Alaq | 19 |
| 97 | Al-Qadr | 5 |
| 98 | Al-Bayyinah | 8 |
| 99 | Az-Zalzalah | 8 |
| 100 | Al-Adiyat | 11 |
| 101 | Al-Qari'ah | 11 |
| 102 | At-Takasur | 8 |
| 103 | Al-Asr | 3 |
| 104 | Al-Humazah | 9 |
| 105 | Al-Fil | 5 |
| 106 | Quraisy | 4 |
| 107 | Al-Ma'un | 7 |
| 108 | Al-Kausar | 3 |
| 109 | Al-Kafirun | 6 |
| 110 | An-Nasr | 3 |
| 111 | Al-Lahab | 5 |
| 112 | Al-Ikhlas | 4 |
| 113 | Al-Falaq | 5 |
| 114 | An-Nas | 6 |

## Contoh Query

### Mendapatkan semua hafalan siswa tertentu
```sql
SELECT h.*, s.nama as nama_siswa, g.nama as nama_guru
FROM hafalan h
JOIN siswa s ON h.siswa_id = s.id
JOIN guru g ON h.guru_id = g.id
WHERE s.id = 1
ORDER BY h.tanggal DESC;
```

### Mendapatkan statistik hafalan per siswa
```sql
SELECT 
    s.nama, 
    COUNT(*) as total_hafalan,
    SUM(CASE WHEN h.status = 'lancar' THEN 1 ELSE 0 END) as lancar,
    SUM(CASE WHEN h.status = 'perlu bimbingan' THEN 1 ELSE 0 END) as perlu_bimbingan,
    SUM(CASE WHEN h.status = 'mengulang' THEN 1 ELSE 0 END) as mengulang
FROM hafalan h
JOIN siswa s ON h.siswa_id = s.id
GROUP BY s.id, s.nama
ORDER BY total_hafalan DESC;
```

### Mendapatkan hafalan terbaru
```sql
SELECT h.*, s.nama as nama_siswa, g.nama as nama_guru
FROM hafalan h
JOIN siswa s ON h.siswa_id = s.id
JOIN guru g ON h.guru_id = g.id
ORDER BY h.tanggal DESC, h.created_at DESC
LIMIT 10;
```

### Mendapatkan surah yang paling banyak dihafal
```sql
SELECT 
    surah_id, 
    COUNT(*) as total_hafalan
FROM hafalan
GROUP BY surah_id
ORDER BY total_hafalan DESC
LIMIT 10;
```

## Statistik Data Seed

- **Total Hafalan**: 74
- **Total Siswa**: 10
- **Rata-rata Hafalan per Siswa**: 7.4
- **Distribusi Status**:
  - Lancar: ~35%
  - Perlu Bimbingan: ~34%
  - Mengulang: ~31%

## Migration Order

Tabel `hafalan` harus dibuat setelah:
1. `users` (diperlukan untuk siswa dan guru)
2. `guru` (foreign key guru_id)
3. `kelas` (diperlukan untuk siswa)
4. `siswa` (foreign key siswa_id)

## Seeder Order

`HafalanSeeder` harus dijalankan setelah:
1. `OtherRolesSeeder`
2. `GuruSeeder`
3. `KelasSeeder`
4. `SiswaSeeder`
