# Custom Page Builder - Quick Start Guide

## 📋 Overview
Fitur Custom Page Builder memungkinkan admin dan super-admin untuk membuat halaman dinamis dengan konten HTML yang dapat dikonfigurasi aksesnya berdasarkan role user.

## 🎯 Features
- ✅ Create, Read, Update, Delete custom pages
- ✅ Role-based access control untuk setiap halaman
- ✅ Support multiple roles per halaman
- ✅ HTML content dengan longText support
- ✅ Pagination dan search functionality
- ✅ Security logging untuk unauthorized attempts
- ✅ Validasi input yang ketat
- ✅ RESTful API dengan JSON response

## 🚀 Installation

### 1. Run Migration
```bash
php artisan migrate
```

Migration akan membuat tabel `custom_pages` dengan struktur:
- `id` (bigint)
- `title` (varchar 255)
- `html_content` (longtext)
- `role` (json array)
- `timestamps`

### 2. Verify Routes
Routes sudah ditambahkan di `routes/api.php`:
```
GET    /api/v1/custom-pages          - List pages
GET    /api/v1/custom-pages/{id}     - Show page
POST   /api/v1/custom-pages          - Create page (admin/super-admin)
PUT    /api/v1/custom-pages/{id}     - Update page (admin/super-admin)
DELETE /api/v1/custom-pages/{id}     - Delete page (admin/super-admin)
```

## 📁 File Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   └── CustomPageController.php          # Main controller
│   ├── Requests/
│   │   ├── StoreCustomPageRequest.php        # Create validation
│   │   └── UpdateCustomPageRequest.php       # Update validation
│   └── Resources/
│       └── CustomPageResource.php            # JSON response format
└── Models/
    └── CustomPage.php                        # Eloquent model

database/
└── migrations/
    └── 2025_10_26_172324_create_custom_pages_table.php

routes/
└── api.php                                   # API routes
```

## 🔐 Authorization Rules

### Create, Update, Delete
```php
// Hanya admin dan super-admin
if (!in_array($user->role, ['admin', 'super-admin'])) {
    return 403; // Forbidden
}
```

### View (Index & Show)
```php
// Admin & Super-admin: Dapat melihat SEMUA halaman
// Role lain: Hanya halaman yang role-nya terdaftar

if (!in_array($user->role, ['admin', 'super-admin'])) {
    // Filter by role
    $query->viewableByUser($user);
}
```

## 💻 Usage Examples

### 1. Create Page for Students (Admin only)
```bash
POST /api/v1/custom-pages
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "title": "Panduan Siswa",
  "html_content": "<h1>Selamat Datang</h1><p>Ini adalah panduan...</p>",
  "role": ["siswa", "orang-tua"]
}
```

### 2. Get All Pages (Based on User Role)
```bash
GET /api/v1/custom-pages
Authorization: Bearer {token}

# Admin: Melihat SEMUA halaman
# Siswa: Hanya melihat halaman dengan role "siswa"
# Guru: Hanya melihat halaman dengan role "guru"
```

### 3. Update Page (Admin only)
```bash
PUT /api/v1/custom-pages/1
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "title": "Panduan Siswa (Updated)",
  "html_content": "<h1>Update</h1>"
}
```

### 4. Delete Page (Admin only)
```bash
DELETE /api/v1/custom-pages/1
Authorization: Bearer {admin_token}
```

## 🧪 Testing

### Run Test Script
```bash
php test_custom_pages.php
```

Test script akan melakukan:
- ✅ Login sebagai admin dan siswa
- ✅ Create 3 custom pages dengan role berbeda
- ✅ Get all pages (admin & siswa view)
- ✅ Update page
- ✅ Delete page
- ✅ Test unauthorized access
- ✅ Test validation errors

### Manual Testing dengan cURL

#### Login sebagai Admin
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

#### Create Custom Page
```bash
curl -X POST http://localhost:8000/api/v1/custom-pages \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Page",
    "html_content": "<h1>Hello World</h1>",
    "role": ["siswa", "guru"]
  }'
```

#### Get All Pages
```bash
curl -X GET http://localhost:8000/api/v1/custom-pages \
  -H "Authorization: Bearer {token}"
```

## 🎨 Available Roles
```php
[
    'siswa',
    'orang-tua',
    'guru',
    'wali-kelas',
    'kepala-sekolah',
    'tata-usaha',
    'yayasan',
    'admin',
    'super-admin'
]
```

## 🔍 Search & Pagination

### Search by Title
```bash
GET /api/v1/custom-pages?search=panduan
```

### Pagination
```bash
GET /api/v1/custom-pages?per_page=10&page=2
```

### Combine Search & Pagination
```bash
GET /api/v1/custom-pages?search=guru&per_page=5
```

## ⚠️ Security Considerations

### 1. HTML Content Security
Backend menyimpan HTML as-is. Di frontend, disarankan untuk:
- Menggunakan HTML sanitizer (DOMPurify, dll)
- Membatasi tag HTML yang diperbolehkan
- Implement Content Security Policy (CSP)

### 2. Role Validation
- Role array tidak boleh kosong
- Hanya role yang valid yang diterima
- Validasi dilakukan di Request class

### 3. Access Logging
Setiap unauthorized attempt dicatat di log:
```php
Log::channel('security')->warning('Unauthorized attempt', [
    'user_id' => $user->id,
    'user_role' => $user->role,
    'page_id' => $pageId,
    'ip' => $request->ip(),
]);
```

## 📊 Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    "id": 1,
    "title": "Page Title",
    "html_content": "<h1>Content</h1>",
    "role": ["siswa", "guru"],
    "created_at": "2025-10-26T10:30:00Z",
    "updated_at": "2025-10-26T10:30:00Z"
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message"
}
```

### Validation Error Response
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

## 🛠️ Model Methods

### Check if Role Can View Page
```php
$page->canBeViewedByRole('siswa'); // true/false
```

### Check if User Can View Page
```php
$page->canBeViewedByUser($user); // true/false
```

### Query Scope - Get Viewable Pages by Role
```php
CustomPage::viewableByRole('siswa')->get();
```

### Query Scope - Get Viewable Pages by User
```php
CustomPage::viewableByUser($user)->get();
```

## 📝 Best Practices

1. **Always sanitize HTML in frontend** - Backend menyimpan raw HTML
2. **Use pagination** - Untuk performa yang lebih baik
3. **Implement search** - Memudahkan user mencari halaman
4. **Log security events** - Semua unauthorized attempts sudah di-log
5. **Validate roles** - Pastikan role array tidak kosong dan valid
6. **Use resource classes** - Untuk konsistensi response format

## 🐛 Troubleshooting

### Error: "Unauthenticated"
- Pastikan token valid dan belum expired
- Check header: `Authorization: Bearer {token}`

### Error: "Unauthorized"
- Pastikan user memiliki role yang tepat
- Create/Update/Delete: Hanya admin & super-admin
- View: Berdasarkan konfigurasi role di halaman

### Error: "Custom page not found"
- Pastikan ID halaman benar
- Halaman mungkin sudah dihapus

### Validation Error
- Check request body format
- Pastikan role array tidak kosong
- Pastikan role yang diisi valid

## 📚 Documentation
Full API documentation: `CUSTOM_PAGE_BUILDER_API_DOCUMENTATION.md`

## 🤝 Support
Untuk issue atau pertanyaan, silakan hubungi tim development.

---

**Version:** 1.0.0  
**Last Updated:** October 26, 2025
