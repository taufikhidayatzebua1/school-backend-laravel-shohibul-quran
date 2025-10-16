# Query Optimization & N+1 Problem Prevention Guide

## Overview

Dokumentasi ini menjelaskan bagaimana menghindari N+1 problem dan optimasi query pada API Hafalan.

## N+1 Problem

### Apa itu N+1 Problem?

N+1 problem terjadi ketika:
1. Query pertama mengambil N records
2. Kemudian untuk setiap record, Laravel melakukan query tambahan untuk mengambil relasi
3. Total query = 1 (main query) + N (relasi queries) = N+1 queries

**Contoh BAD:**
```php
// 1 query untuk hafalan
$hafalan = Hafalan::limit(5)->get();

// 5 query untuk siswa (1 per hafalan)
// 5 query untuk guru (1 per hafalan)
// 5 query untuk kelas (1 per siswa)
foreach ($hafalan as $h) {
    echo $h->siswa->nama;        // Query ke database
    echo $h->siswa->kelas->nama; // Query ke database
    echo $h->guru->nama;         // Query ke database
}
// Total: 1 + 5 + 5 + 5 = 16 queries!
```

## Solution: Eager Loading

### 1. Basic Eager Loading

**GOOD:**
```php
$hafalan = Hafalan::with(['siswa', 'guru'])->get();
// Query 1: SELECT * FROM hafalan
// Query 2: SELECT * FROM siswa WHERE id IN (1,2,3,4,5)
// Query 3: SELECT * FROM guru WHERE id IN (1,2,3,4,5)
// Total: 3 queries only!
```

### 2. Nested Eager Loading

**BETTER:**
```php
$hafalan = Hafalan::with([
    'siswa',
    'siswa.kelas',
    'guru'
])->get();
// Query 1: SELECT * FROM hafalan
// Query 2: SELECT * FROM siswa WHERE id IN (...)
// Query 3: SELECT * FROM kelas WHERE id IN (...)
// Query 4: SELECT * FROM guru WHERE id IN (...)
// Total: 4 queries
```

### 3. Selective Column Loading

**BEST:**
```php
$hafalan = Hafalan::with([
    'siswa' => function ($query) {
        $query->select('id', 'nis', 'nama', 'kelas_id');
    },
    'siswa.kelas' => function ($query) {
        $query->select('id', 'nama_kelas', 'tahun_ajaran');
    },
    'guru' => function ($query) {
        $query->select('id', 'nip', 'nama');
    }
])->get();
// Same 4 queries but with less data transfer
// Faster performance!
```

## Filter by Kelas

### Method 1: whereHas (Recommended)

```php
// OPTIMIZED - Filter at database level
$hafalan = Hafalan::with([
    'siswa.kelas',
    'guru'
])
->whereHas('siswa', function ($q) {
    $q->where('kelas_id', 1);
})
->get();
```

**Advantages:**
- ✅ Filter di database level (efficient)
- ✅ Hanya data yang relevan yang diambil
- ✅ Performant untuk dataset besar

### Method 2: Filter After Loading (NOT Recommended)

```php
// BAD - Load all then filter in PHP
$hafalan = Hafalan::with(['siswa.kelas', 'guru'])->get();
$filtered = $hafalan->filter(function ($h) {
    return $h->siswa && $h->siswa->kelas_id == 1;
});
```

**Disadvantages:**
- ❌ Memuat semua data ke memory
- ❌ Filter di PHP level (slow)
- ❌ Waste bandwidth dan resources

## API Implementation

### Controller Method (HafalanController.php)

```php
public function index(Request $request): JsonResponse
{
    // Eager load dengan selective columns
    $query = Hafalan::with([
        'siswa' => function ($query) {
            $query->select('id', 'user_id', 'nis', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'alamat', 'kelas_id');
        },
        'siswa.kelas' => function ($query) {
            $query->select('id', 'nama_kelas', 'wali_kelas_id', 'tahun_ajaran');
        },
        'guru' => function ($query) {
            $query->select('id', 'user_id', 'nip', 'nama', 'jenis_kelamin', 'no_hp');
        }
    ]);

    // Filter by kelas_id using whereHas
    if ($request->has('kelas_id')) {
        $query->whereHas('siswa', function ($q) use ($request) {
            $q->where('kelas_id', $request->kelas_id);
        });
    }

    // ... other filters

    return response()->json([
        'success' => true,
        'data' => $query->paginate($request->get('per_page', 15))
    ]);
}
```

## API Usage Examples

### Get Hafalan by Kelas ID

```bash
GET /api/hafalan?kelas_id=1
```

**PowerShell:**
```powershell
Invoke-WebRequest -Uri 'http://localhost:8000/api/hafalan?kelas_id=1' `
    -Method GET `
    -Headers @{Accept='application/json'}
```

**Response:**
```json
{
  "success": true,
  "message": "Data hafalan berhasil diambil",
  "data": {
    "data": [
      {
        "id": 1,
        "siswa": {
          "id": 1,
          "nama": "Andi Wijaya",
          "nis": "2024001",
          "kelas": {
            "id": 1,
            "nama_kelas": "X IPA 1",
            "tahun_ajaran": "2024/2025"
          }
        },
        "guru": {
          "id": 2,
          "nama": "Siti Aminah, S.Pd"
        }
      }
    ],
    "total": 12
  }
}
```

### Combine Multiple Filters

```bash
GET /api/hafalan?kelas_id=1&status=lancar&per_page=10
```

## Performance Testing

### Test Script (test_n1_problem.php)

Jalankan untuk melihat perbedaan performa:

```bash
php test_n1_problem.php
```

**Expected Results:**
```
WITH eager loading:    4 queries
WITHOUT eager loading: 16 queries
Optimization saved:    12 queries (75% reduction!)
```

## Query Analysis

### Optimized Queries (4 total)

1. **Main Query** - Get hafalan with filter
   ```sql
   SELECT * FROM hafalan 
   WHERE EXISTS (
     SELECT * FROM siswa 
     WHERE hafalan.siswa_id = siswa.id 
     AND kelas_id = 1
   ) LIMIT 5
   ```

2. **Siswa Query** - Batch load siswa
   ```sql
   SELECT id, nis, nama, kelas_id FROM siswa 
   WHERE id IN (1, 2)
   ```

3. **Kelas Query** - Batch load kelas
   ```sql
   SELECT id, nama_kelas, tahun_ajaran FROM kelas 
   WHERE id IN (1)
   ```

4. **Guru Query** - Batch load guru
   ```sql
   SELECT id, nip, nama FROM guru 
   WHERE id IN (2, 3, 7, 9)
   ```

### Non-Optimized (16 queries)

Without eager loading, Laravel would execute:
- 1 query for hafalan
- 5 queries for siswa (one per hafalan)
- 5 queries for kelas (one per siswa)
- 5 queries for guru (one per hafalan)

## Best Practices Checklist

- ✅ Always use `with()` for relationships
- ✅ Use `whereHas()` for filtering by relationship
- ✅ Select only needed columns
- ✅ Use pagination for large datasets
- ✅ Test queries with `DB::enableQueryLog()`
- ✅ Monitor query count in development
- ✅ Use Laravel Debugbar for analysis

## Additional Optimizations

### 1. Index Database Columns

```sql
-- Add indexes for frequently queried columns
ALTER TABLE hafalan ADD INDEX idx_siswa_id (siswa_id);
ALTER TABLE hafalan ADD INDEX idx_guru_id (guru_id);
ALTER TABLE hafalan ADD INDEX idx_tanggal (tanggal);
ALTER TABLE siswa ADD INDEX idx_kelas_id (kelas_id);
```

### 2. Query Caching (Future Enhancement)

```php
use Illuminate\Support\Facades\Cache;

$hafalan = Cache::remember("hafalan.kelas.{$kelasId}", 3600, function () use ($kelasId) {
    return Hafalan::with(['siswa.kelas', 'guru'])
        ->whereHas('siswa', function ($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        })
        ->get();
});
```

### 3. Lazy Load Prevention

Add to Model:
```php
protected $with = ['siswa', 'guru']; // Auto eager load
```

## Monitoring Tools

### Laravel Debugbar
```bash
composer require barryvdh/laravel-debugbar --dev
```

### Laravel Telescope
```bash
composer require laravel/telescope
php artisan telescope:install
```

## Conclusion

Dengan implementasi eager loading dan selective column loading:
- ✅ **75% reduction** dalam jumlah queries
- ✅ **Faster response time**
- ✅ **Lower database load**
- ✅ **Better scalability**

Query count: **4 queries** instead of **16 queries**! 🚀
