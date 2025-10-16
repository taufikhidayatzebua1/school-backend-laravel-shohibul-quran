# SQ Backend - Hafalan Al-Quran Management API

Backend API untuk sistem manajemen hafalan Al-Quran dengan Laravel 11, Laravel Sanctum, dan fitur-fitur production-ready.

## 🚀 Features

### Authentication & Authorization
- ✅ **Register** - Pendaftaran user baru dengan validasi
- ✅ **Login** - Login dengan email & password (token 24 jam)
- ✅ **Logout** - Logout dan revoke token
- ✅ **Get Profile** - Ambil data profile user
- ✅ **Update Profile** - Update nama, email, atau password
- ✅ **Role-Based Access Control** - 9 level role untuk akses control

### Hafalan Management
- ✅ **CRUD Hafalan** - Create, Read, Update, Delete data hafalan
- ✅ **CRUD Siswa** - Manajemen data siswa
- ✅ **CRUD Kelas** - Manajemen data kelas
- ✅ **CRUD Guru** - Manajemen data guru
- ✅ **Public API** - Endpoint publik dengan data terbatas
- ✅ **Protected API** - Endpoint terproteksi dengan data lengkap

### Production-Ready Features
- ✅ **API Documentation** - Auto-generated dengan Scribe @ `/api/v1/docs`
- ✅ **Response Caching** - 30 menit untuk public endpoints
- ✅ **Rate Limiting** - 60/10/200 requests per menit
- ✅ **Request ID Tracking** - UUID untuk debugging
- ✅ **Query Optimization** - Eager loading, no N+1 problem
- ✅ **Form Validation** - 9 request classes dengan validasi lengkap
- ✅ **API Resources** - Public & Protected data transformation
- ✅ **Security Headers** - XSS, clickjacking, MIME sniffing protection
- ✅ **Logging** - Security log dengan request ID

## 👥 User Roles

Sistem mendukung 9 jenis role:

1. **siswa** - Siswa/pelajar
2. **orang-tua** - Orang tua siswa
3. **guru** - Guru/pengajar
4. **wali-kelas** - Wali kelas (NEW)
5. **kepala-sekolah** - Kepala sekolah
6. **tata-usaha** - Staff tata usaha
7. **yayasan** - Pihak yayasan
8. **admin** - Administrator
9. **super-admin** - Super administrator

**⚠️ Important:** Setiap role terpisah dan tidak saling include. Method `isAdmin()` hanya check role `admin`, TIDAK termasuk `super-admin`.

**Dokumentasi lengkap:**
- `ROLE_SYSTEM.md` - Complete role system documentation
- `ROLE_EXAMPLES.md` - Contoh penggunaan dan best practices

## 🧪 Testing

Sistem dilengkapi dengan comprehensive test suite (38 tests):

### Quick Test
```bash
# Start server
php artisan serve

# Run all tests
php run_all_tests.php
```

### Test Files
- `test_authentication.php` - Auth & authorization (7 tests)
- `test_api_errors.php` - Error responses (5 tests)
- `test_caching.php` - Response caching (5 tests)
- `test_resources.php` - Data limiting (5 tests)
- `test_validation.php` - Form validation (7 tests)
- `test_rate_limiting.php` - Rate limiting (2 tests)
- `test_security_headers.php` - Security headers (6 tests)
- `test_n1_problem.php` - Query optimization (1 test)

**Test Results**: ✅ 38/38 tests passing (100%)

**Dokumentasi:**
- `TESTING_GUIDE.md` - Complete testing guide
- `TEST_RESULTS.md` - Detailed test results
- `TEST_FILES_SUMMARY.md` - Test files overview

## 📋 Requirements

- PHP >= 8.2
- Composer
- MySQL Database
- Laravel 11

## ⚙️ Configuration

### Database Configuration
Database sudah dikonfigurasi di `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=livezet.id
DB_PORT=3306
DB_DATABASE=hizetmyi_sqtest1
DB_USERNAME=hizetmyi_sqtest1
DB_PASSWORD=hizetmyi_sqtest1
```

### Email Configuration
Email SMTP sudah dikonfigurasi untuk forgot password:
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.livezet.id
MAIL_PORT=465
MAIL_USERNAME=sqtest1@livezet.id
MAIL_PASSWORD=hizetmyi_sqtest1
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="sqtest1@livezet.id"
```

## 🚀 Running the Application

Start development server:
```bash
php artisan serve
```

Server akan berjalan di: `http://127.0.0.1:8000`

## 📖 API Documentation

Base URL: `http://localhost:8000/api`

### Public Endpoints (No Authentication)

#### 1. Register
```http
POST /api/auth/register
```

**Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "access_token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
        "token_type": "Bearer"
    }
}
```

#### 2. Login
```http
POST /api/auth/login
```

**Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

#### 3. Forgot Password
```http
POST /api/auth/forgot-password
```

**Body:**
```json
{
    "email": "john@example.com"
}
```

#### 4. Reset Password
```http
POST /api/auth/reset-password
```

**Body:**
```json
{
    "token": "token-from-email",
    "email": "john@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

### Protected Endpoints (Require Authentication)

**Headers Required:**
```
Authorization: Bearer {your_access_token}
Accept: application/json
```

#### 5. Get Profile
```http
GET /api/auth/profile
```

#### 6. Update Profile
```http
PUT /api/auth/profile
```

**Body (Update Name/Email):**
```json
{
    "name": "John Updated",
    "email": "johnupdated@example.com"
}
```

**Body (Update Password):**
```json
{
    "current_password": "password123",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

#### 7. Logout
```http
POST /api/auth/logout
```

#### 8. Revoke All Tokens
```http
POST /api/auth/revoke-tokens
```

## 🧪 Testing with PowerShell

### Register
```powershell
$body = @{
    name='John Doe'
    email='john@example.com'
    password='password123'
    password_confirmation='password123'
} | ConvertTo-Json

$response = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/auth/register' `
    -Method POST -Body $body `
    -ContentType 'application/json' `
    -Headers @{Accept='application/json'}
```

### Login & Save Token
```powershell
$body = @{
    email='john@example.com'
    password='password123'
} | ConvertTo-Json

$response = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/auth/login' `
    -Method POST -Body $body `
    -ContentType 'application/json' `
    -Headers @{Accept='application/json'}

$token = $response.data.access_token
```

### Get Profile
```powershell
$response = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/auth/profile' `
    -Method GET `
    -Headers @{
        Accept='application/json'
        Authorization="Bearer $token"
    }
```

## 📁 Project Structure

```
sq-backend/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           └── AuthController.php      # Authentication controller
│   └── Models/
│       └── User.php                         # User model with HasApiTokens
├── routes/
│   └── api.php                              # API routes
├── database/
│   └── migrations/                          # Database migrations
├── .env                                     # Environment configuration
├── API_DOCUMENTATION.md                     # Detailed API docs
├── postman_collection.json                  # Postman collection
└── README.md                                # This file
```

## 📝 API Endpoints Summary

| Method | Endpoint | Auth Required | Description |
|--------|----------|---------------|-------------|
| POST | `/api/auth/register` | No | Register new user |
| POST | `/api/auth/login` | No | Login user |
| POST | `/api/auth/forgot-password` | No | Send reset link |
| POST | `/api/auth/reset-password` | No | Reset password |
| GET | `/api/auth/profile` | Yes | Get user profile |
| PUT | `/api/auth/profile` | Yes | Update profile |
| POST | `/api/auth/logout` | Yes | Logout current device |
| POST | `/api/auth/revoke-tokens` | Yes | Logout all devices |

## 🔧 Useful Commands

### Run migrations
```bash
php artisan migrate
```

### Clear cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### View routes
```bash
php artisan route:list
```

## 📦 Postman Collection

Import file `postman_collection.json` ke Postman untuk testing yang lebih mudah.

Atau lihat dokumentasi lengkap di `API_DOCUMENTATION.md`

## 📱 Integrasi dengan Ionic/Angular

API ini sudah siap digunakan untuk aplikasi mobile Android/iOS yang dibuat dengan Ionic!

**Fitur yang cocok untuk Ionic:**
- ✅ Token-Based Authentication (Bearer Token)
- ✅ RESTful API dengan JSON response
- ✅ CORS ready untuk cross-origin requests
- ✅ Stateless - cocok untuk mobile apps
- ✅ Response format konsisten

**Lihat dokumentasi lengkap:** `IONIC_INTEGRATION.md`

File tersebut berisi:
- Complete Auth Service untuk Ionic/Angular
- Auth Guard implementation
- Example pages (Login, Register, Profile)
- HTTP Interceptor untuk auto token injection
- Routing configuration
- Best practices untuk mobile development

## ✅ Testing Results

Semua endpoint sudah di-test dan berfungsi dengan baik:

- ✅ **Register** - Berhasil membuat user baru dan return token
- ✅ **Login** - Berhasil login dan return token
- ✅ **Get Profile** - Berhasil ambil data user dengan token
- ✅ **Update Profile** - Berhasil update nama user
- ✅ **Logout** - Berhasil logout dan revoke token
- ⚠️ **Forgot Password** - Email functionality (perlu verifikasi SMTP settings untuk production)

## 🔒 Security Notes

1. Access tokens tidak expire secara default di Sanctum
2. Untuk production, pertimbangkan:
   - Menambahkan rate limiting
   - Mengaktifkan HTTPS
   - Menambahkan token expiration
   - Menggunakan queue untuk mengirim email
   - Menambahkan email verification

## 📧 Email Configuration

Email sudah dikonfigurasi menggunakan SMTP server livezet.id dengan SSL encryption pada port 465. Forgot password akan mengirim email ke user dengan link reset password.

---

**Built with ❤️ using Laravel 12 & Laravel Sanctum**
