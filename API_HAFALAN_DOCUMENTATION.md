# API Documentation - Hafalan CRUD

Base URL: `http://localhost:8000/api`

## Endpoints

### 1. Get All Hafalan (with pagination & filters)
**GET** `/hafalan`

**Query Parameters:**
- `siswa_id` (optional) - Filter by siswa ID
- `guru_id` (optional) - Filter by guru ID
- `status` (optional) - Filter by status (lancar, perlu bimbingan, mengulang)
- `surah_id` (optional) - Filter by surah ID
- `tanggal_dari` (optional) - Filter by date from (Y-m-d)
- `tanggal_sampai` (optional) - Filter by date to (Y-m-d)
- `sort_by` (optional) - Sort field (default: tanggal)
- `sort_order` (optional) - Sort order (asc/desc, default: desc)
- `per_page` (optional) - Items per page (default: 15)

**Example Request:**
```bash
GET /api/hafalan
GET /api/hafalan?siswa_id=1
GET /api/hafalan?status=lancar&per_page=10
GET /api/hafalan?tanggal_dari=2025-10-01&tanggal_sampai=2025-10-15
```

**Example Response:**
```json
{
  "success": true,
  "message": "Data hafalan berhasil diambil",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 75,
        "siswa_id": 1,
        "guru_id": 2,
        "surah_id": 112,
        "ayat_dari": 1,
        "ayat_sampai": 4,
        "status": "lancar",
        "tanggal": "2025-10-15T00:00:00.000000Z",
        "keterangan": "Hafalan sangat baik",
        "created_at": "2025-10-15T14:28:08.000000Z",
        "updated_at": "2025-10-15T14:28:08.000000Z",
        "siswa": {
          "id": 1,
          "nama": "Andi Wijaya",
          "nis": "2024001"
        },
        "guru": {
          "id": 2,
          "nama": "Siti Aminah, S.Pd",
          "nip": "198203152005022001"
        }
      }
    ],
    "total": 75,
    "per_page": 15,
    "current_page": 1,
    "last_page": 5
  }
}
```

---

### 2. Get Hafalan by ID
**GET** `/hafalan/{id}`

**Example Request:**
```bash
GET /api/hafalan/75
```

**Example Response:**
```json
{
  "success": true,
  "message": "Data hafalan berhasil diambil",
  "data": {
    "id": 75,
    "siswa_id": 1,
    "guru_id": 2,
    "surah_id": 112,
    "ayat_dari": 1,
    "ayat_sampai": 4,
    "status": "lancar",
    "tanggal": "2025-10-15T00:00:00.000000Z",
    "keterangan": "Hafalan sangat baik",
    "siswa": { ... },
    "guru": { ... }
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Hafalan tidak ditemukan"
}
```

---

### 3. Create New Hafalan
**POST** `/hafalan`

**Request Body:**
```json
{
  "siswa_id": 1,
  "guru_id": 2,
  "surah_id": 112,
  "ayat_dari": 1,
  "ayat_sampai": 4,
  "status": "lancar",
  "tanggal": "2025-10-15",
  "keterangan": "Hafalan Al-Ikhlas sangat lancar dan sempurna"
}
```

**Validation Rules:**
- `siswa_id`: required, must exist in siswa table
- `guru_id`: required, must exist in guru table
- `surah_id`: required, integer, min: 1, max: 114
- `ayat_dari`: required, integer, min: 1
- `ayat_sampai`: required, integer, min: 1, must be >= ayat_dari
- `status`: required, enum: 'lancar', 'perlu bimbingan', 'mengulang'
- `tanggal`: required, date format
- `keterangan`: optional, string

**Example Response (201):**
```json
{
  "success": true,
  "message": "Hafalan berhasil ditambahkan",
  "data": {
    "id": 75,
    "siswa_id": 1,
    "guru_id": 2,
    ...
  }
}
```

**Error Response (422):**
```json
{
  "success": false,
  "message": "Ayat sampai harus lebih besar atau sama dengan ayat dari",
  "errors": {
    "ayat_sampai": [
      "Ayat sampai harus lebih besar atau sama dengan ayat dari"
    ]
  }
}
```

---

### 4. Update Hafalan
**PUT** `/hafalan/{id}`

**Request Body:** (all fields are optional)
```json
{
  "status": "perlu bimbingan",
  "keterangan": "Perlu perbaikan di makhraj huruf Qof"
}
```

**Validation Rules:** (same as create, but all fields are optional)
- `siswa_id`: sometimes|required, must exist in siswa table
- `guru_id`: sometimes|required, must exist in guru table
- `surah_id`: sometimes|required, integer, min: 1, max: 114
- `ayat_dari`: sometimes|required, integer, min: 1
- `ayat_sampai`: sometimes|required, integer, min: 1
- `status`: sometimes|required, enum: 'lancar', 'perlu bimbingan', 'mengulang'
- `tanggal`: sometimes|required, date format
- `keterangan`: optional, string

**Example Response:**
```json
{
  "success": true,
  "message": "Hafalan berhasil diupdate",
  "data": { ... }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Hafalan tidak ditemukan"
}
```

---

### 5. Delete Hafalan
**DELETE** `/hafalan/{id}`

**Example Request:**
```bash
DELETE /api/hafalan/75
```

**Example Response:**
```json
{
  "success": true,
  "message": "Hafalan berhasil dihapus"
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Hafalan tidak ditemukan"
}
```

---

### 6. Get Hafalan Statistics
**GET** `/hafalan/statistics`

**Query Parameters:**
- `siswa_id` (optional) - Get statistics for specific siswa
- `guru_id` (optional) - Get statistics for specific guru

**Example Request:**
```bash
GET /api/hafalan/statistics
GET /api/hafalan/statistics?siswa_id=1
GET /api/hafalan/statistics?guru_id=2
```

**Example Response:**
```json
{
  "success": true,
  "message": "Statistik hafalan berhasil diambil",
  "data": {
    "total_hafalan": 75,
    "lancar": 27,
    "perlu_bimbingan": 26,
    "mengulang": 22
  }
}
```

---

## Status Enum Values

| Status | Keterangan |
|--------|------------|
| `lancar` | Hafalan sangat baik, bacaan tartil dan makhraj jelas |
| `perlu bimbingan` | Hafalan cukup baik, namun perlu perbaikan |
| `mengulang` | Hafalan masih terbata-bata, perlu mengulang |

---

## Surah ID Reference

Surah ID menggunakan nomor urut dalam Al-Quran (1-114):
- 1: Al-Fatihah
- 2: Al-Baqarah
- 3: Ali 'Imran
- ...
- 112: Al-Ikhlas
- 113: Al-Falaq
- 114: An-Nas

Lihat `HAFALAN_DOCUMENTATION.md` untuk daftar lengkap surah yang digunakan dalam seeder.

---

## Example PowerShell Requests

### Get All
```powershell
Invoke-WebRequest -Uri 'http://localhost:8000/api/hafalan' -Method GET -Headers @{Accept='application/json'}
```

### Get Statistics
```powershell
Invoke-WebRequest -Uri 'http://localhost:8000/api/hafalan/statistics' -Method GET -Headers @{Accept='application/json'}
```

### Create
```powershell
$body = @{
    siswa_id = 1
    guru_id = 2
    surah_id = 112
    ayat_dari = 1
    ayat_sampai = 4
    status = 'lancar'
    tanggal = '2025-10-15'
    keterangan = 'Hafalan Al-Ikhlas sangat lancar'
} | ConvertTo-Json

Invoke-WebRequest -Uri 'http://localhost:8000/api/hafalan' `
    -Method POST `
    -Body $body `
    -ContentType 'application/json' `
    -Headers @{Accept='application/json'}
```

### Update
```powershell
$body = @{
    status = 'perlu bimbingan'
    keterangan = 'Perlu perbaikan makhraj'
} | ConvertTo-Json

Invoke-WebRequest -Uri 'http://localhost:8000/api/hafalan/75' `
    -Method PUT `
    -Body $body `
    -ContentType 'application/json' `
    -Headers @{Accept='application/json'}
```

### Delete
```powershell
Invoke-WebRequest -Uri 'http://localhost:8000/api/hafalan/75' `
    -Method DELETE `
    -Headers @{Accept='application/json'}
```

---

## Notes

- ✅ No authentication required
- ✅ All endpoints return JSON
- ✅ Pagination enabled on list endpoint
- ✅ Multiple filters available
- ✅ Includes related data (siswa & guru)
- ✅ Full CRUD operations
- ✅ Statistics endpoint for analytics
