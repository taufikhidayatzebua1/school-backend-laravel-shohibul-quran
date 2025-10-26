# Custom Page Builder API Documentation

## Overview
API untuk mengelola halaman custom dengan HTML content yang dapat dikonfigurasi aksesnya berdasarkan role user.

**Base URL:** `/api/v1/custom-pages`

## Authorization

### Create, Update, Delete
- **Required Role:** `admin` atau `super-admin`
- Hanya admin dan super-admin yang dapat membuat, mengupdate, dan menghapus custom pages

### View (Index & Show)
- **Required:** Authenticated user
- **Access Control:**
  - `admin` dan `super-admin`: dapat melihat semua halaman
  - Role lain: hanya dapat melihat halaman yang role-nya terdaftar di field `role` pada halaman tersebut

---

## Endpoints

### 1. Get All Custom Pages
**GET** `/api/v1/custom-pages`

Mendapatkan daftar semua custom pages (berdasarkan role user).

#### Headers
```
Authorization: Bearer {token}
Accept: application/json
```

#### Query Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| per_page | integer | No | Jumlah item per halaman (default: 15) |
| search | string | No | Cari berdasarkan title |

#### Response Success (200)
```json
{
  "success": true,
  "message": "Custom pages retrieved successfully",
  "data": [
    {
      "id": 1,
      "title": "Panduan Siswa",
      "html_content": "<h1>Selamat Datang</h1><p>Ini adalah panduan untuk siswa...</p>",
      "role": ["siswa", "orang-tua"],
      "created_at": "2025-10-26T10:30:00Z",
      "updated_at": "2025-10-26T10:30:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 15,
    "to": 1,
    "total": 1
  }
}
```

---

### 2. Get Single Custom Page
**GET** `/api/v1/custom-pages/{id}`

Mendapatkan detail custom page berdasarkan ID (jika user memiliki akses).

#### Headers
```
Authorization: Bearer {token}
Accept: application/json
```

#### Response Success (200)
```json
{
  "success": true,
  "message": "Custom page retrieved successfully",
  "data": {
    "id": 1,
    "title": "Panduan Siswa",
    "html_content": "<h1>Selamat Datang</h1><p>Ini adalah panduan untuk siswa...</p>",
    "role": ["siswa", "orang-tua"],
    "created_at": "2025-10-26T10:30:00Z",
    "updated_at": "2025-10-26T10:30:00Z"
  }
}
```

#### Response Error (403)
```json
{
  "success": false,
  "message": "Unauthorized. You do not have permission to view this page."
}
```

#### Response Error (404)
```json
{
  "success": false,
  "message": "Custom page not found"
}
```

---

### 3. Create Custom Page
**POST** `/api/v1/custom-pages`

Membuat custom page baru (hanya admin & super-admin).

#### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

#### Request Body
```json
{
  "title": "Panduan Guru",
  "html_content": "<h1>Panduan untuk Guru</h1><p>Berikut adalah panduan lengkap untuk guru...</p>",
  "role": ["guru", "wali-kelas", "kepala-sekolah"]
}
```

#### Field Validation
| Field | Type | Required | Rules |
|-------|------|----------|-------|
| title | string | Yes | Max 255 characters |
| html_content | string | Yes | - |
| role | array | Yes | Min 1 role, Valid roles only |
| role.* | string | Yes | Must be one of available roles |

**Available Roles:**
- siswa
- orang-tua
- guru
- wali-kelas
- kepala-sekolah
- tata-usaha
- yayasan
- admin
- super-admin

#### Response Success (201)
```json
{
  "success": true,
  "message": "Custom page created successfully",
  "data": {
    "id": 2,
    "title": "Panduan Guru",
    "html_content": "<h1>Panduan untuk Guru</h1><p>Berikut adalah panduan lengkap untuk guru...</p>",
    "role": ["guru", "wali-kelas", "kepala-sekolah"],
    "created_at": "2025-10-26T11:00:00Z",
    "updated_at": "2025-10-26T11:00:00Z"
  }
}
```

#### Response Error (403)
```json
{
  "success": false,
  "message": "Unauthorized. Only admin or super-admin can create custom pages."
}
```

#### Response Error (422)
```json
{
  "message": "The role field must be an array.",
  "errors": {
    "role": [
      "Role harus berupa array."
    ]
  }
}
```

---

### 4. Update Custom Page
**PUT** `/api/v1/custom-pages/{id}`

Mengupdate custom page yang sudah ada (hanya admin & super-admin).

#### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

#### Request Body
Semua field bersifat opsional (partial update):

```json
{
  "title": "Panduan Guru (Updated)",
  "html_content": "<h1>Panduan untuk Guru - Update</h1>",
  "role": ["guru", "wali-kelas"]
}
```

#### Response Success (200)
```json
{
  "success": true,
  "message": "Custom page updated successfully",
  "data": {
    "id": 2,
    "title": "Panduan Guru (Updated)",
    "html_content": "<h1>Panduan untuk Guru - Update</h1>",
    "role": ["guru", "wali-kelas"],
    "created_at": "2025-10-26T11:00:00Z",
    "updated_at": "2025-10-26T11:30:00Z"
  }
}
```

#### Response Error (403)
```json
{
  "success": false,
  "message": "Unauthorized. Only admin or super-admin can update custom pages."
}
```

#### Response Error (404)
```json
{
  "success": false,
  "message": "Custom page not found"
}
```

---

### 5. Delete Custom Page
**DELETE** `/api/v1/custom-pages/{id}`

Menghapus custom page (hanya admin & super-admin).

#### Headers
```
Authorization: Bearer {token}
Accept: application/json
```

#### Response Success (200)
```json
{
  "success": true,
  "message": "Custom page deleted successfully"
}
```

#### Response Error (403)
```json
{
  "success": false,
  "message": "Unauthorized. Only admin or super-admin can delete custom pages."
}
```

#### Response Error (404)
```json
{
  "success": false,
  "message": "Custom page not found"
}
```

---

## Use Cases

### Use Case 1: Membuat Halaman untuk Siswa dan Orang Tua
Admin membuat halaman informasi yang hanya bisa dilihat oleh siswa dan orang tua:

```bash
POST /api/v1/custom-pages
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "title": "Informasi Jadwal Ujian",
  "html_content": "<div><h1>Jadwal Ujian Semester Genap</h1><p>Berikut adalah jadwal ujian...</p></div>",
  "role": ["siswa", "orang-tua"]
}
```

### Use Case 2: Membuat Halaman untuk Guru
Admin membuat halaman panduan yang hanya bisa dilihat oleh guru:

```bash
POST /api/v1/custom-pages
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "title": "Panduan Input Nilai",
  "html_content": "<div><h1>Cara Input Nilai</h1><ol><li>Login ke sistem...</li></ol></div>",
  "role": ["guru", "wali-kelas"]
}
```

### Use Case 3: Siswa Melihat Halaman
Siswa login dan melihat halaman yang tersedia untuk mereka:

```bash
GET /api/v1/custom-pages
Authorization: Bearer {siswa_token}

# Response: Hanya menampilkan halaman yang role-nya mengandung "siswa"
```

### Use Case 4: Update Halaman
Admin mengupdate konten halaman:

```bash
PUT /api/v1/custom-pages/1
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "html_content": "<div><h1>Jadwal Ujian Semester Genap (UPDATED)</h1><p>Jadwal telah diubah...</p></div>"
}
```

---

## Security Features

1. **Authentication Required**: Semua endpoint memerlukan Bearer token
2. **Role-Based Authorization**:
   - Create, Update, Delete: Hanya `admin` dan `super-admin`
   - View: Berdasarkan konfigurasi role di setiap halaman
3. **Rate Limiting**: Menggunakan throttle middleware (200 requests per menit untuk protected routes)
4. **Security Logging**: Setiap unauthorized attempt akan dicatat di log
5. **Input Validation**: Semua input divalidasi menggunakan Form Request
6. **XSS Protection**: HTML content disimpan as-is, pastikan melakukan sanitasi di frontend

---

## Best Practices

### 1. HTML Content Security
Meskipun backend menyimpan HTML as-is, disarankan untuk:
- Menggunakan HTML sanitizer di frontend (seperti DOMPurify)
- Membatasi tag HTML yang diperbolehkan
- Menggunakan Content Security Policy (CSP)

### 2. Role Configuration
- Pastikan role array tidak kosong
- Gunakan role yang valid sesuai dengan sistem
- Admin dan super-admin secara default dapat melihat semua halaman

### 3. Pagination
Gunakan pagination untuk performa yang lebih baik:
```
GET /api/v1/custom-pages?per_page=10
```

### 4. Search
Gunakan search untuk mencari halaman berdasarkan title:
```
GET /api/v1/custom-pages?search=panduan
```

---

## Error Handling

Semua response error mengikuti format standar:

```json
{
  "success": false,
  "message": "Error message here"
}
```

Atau untuk validation errors:

```json
{
  "message": "The role field must be an array.",
  "errors": {
    "role": [
      "Role harus berupa array."
    ]
  }
}
```

---

## Database Schema

```sql
CREATE TABLE custom_pages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    html_content LONGTEXT NOT NULL,
    role JSON NOT NULL COMMENT 'Array of roles that can view this page',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

## Testing

Untuk menjalankan migration:
```bash
php artisan migrate
```

Untuk testing API, gunakan Postman atau cURL:
```bash
# Login sebagai admin
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Create custom page
curl -X POST http://localhost:8000/api/v1/custom-pages \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"title":"Test Page","html_content":"<h1>Test</h1>","role":["siswa"]}'
```
