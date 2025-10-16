# Role System Documentation

Sistem role untuk autentikasi user dengan 8 level akses berbeda.

## Available Roles

1. **siswa** - Siswa/pelajar
2. **orang-tua** - Orang tua siswa
3. **guru** - Guru/pengajar
4. **kepala-sekolah** - Kepala sekolah
5. **tata-usaha** - Staff tata usaha
6. **yayasan** - Pihak yayasan
7. **admin** - Administrator
8. **super-admin** - Super administrator

## Database Structure

Kolom `role` menggunakan ENUM dengan default value `siswa`:

```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    role ENUM('siswa','orang-tua','guru','kepala-sekolah','tata-usaha','yayasan','admin','super-admin') DEFAULT 'siswa',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## API Endpoints

### Register with Role

**Endpoint:** `POST /api/auth/register`

**Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "siswa"
}
```

**Validation Rules:**
- `role` is **required**
- `role` must be one of: `siswa`, `orang-tua`, `guru`, `kepala-sekolah`, `tata-usaha`, `yayasan`, `admin`, `super-admin`

**Response (201):**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "siswa",
            "created_at": "2025-10-15T10:00:00.000000Z",
            "updated_at": "2025-10-15T10:00:00.000000Z"
        },
        "access_token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
        "token_type": "Bearer"
    }
}
```

**Response Error (422) - Invalid Role:**
```json
{
    "success": false,
    "message": "Validation errors",
    "errors": {
        "role": [
            "The selected role is invalid."
        ]
    }
}
```

## User Model Helper Methods

File: `app/Models/User.php`

**Setiap role memiliki method tersendiri dan terpisah:**

```php
// Check if user has specific role
$user->hasRole('admin');  // returns true/false

// Check if user has any of the given roles
$user->hasAnyRole(['admin', 'super-admin']);  // returns true/false

// Check specific roles (each role is separate)
$user->isSiswa();           // returns true/false - Only siswa
$user->isOrangTua();        // returns true/false - Only orang-tua
$user->isGuru();            // returns true/false - Only guru
$user->isKepalaSekolah();   // returns true/false - Only kepala-sekolah
$user->isTataUsaha();       // returns true/false - Only tata-usaha
$user->isYayasan();         // returns true/false - Only yayasan
$user->isAdmin();           // returns true/false - Only admin (NOT including super-admin)
$user->isSuperAdmin();      // returns true/false - Only super-admin

// If you need to check multiple roles, use hasAnyRole()
$user->hasAnyRole(['admin', 'super-admin']);  // Check if admin OR super-admin
```

**Note:** Setiap method hanya check role spesifik tersebut. Method `isAdmin()` hanya check role `admin` saja, TIDAK termasuk `super-admin`. Jika Anda ingin check kedua role tersebut, gunakan `hasAnyRole(['admin', 'super-admin'])`.

## Role Middleware

Middleware untuk membatasi akses berdasarkan role.

**File:** `app/Http/Middleware/CheckRole.php`

### Usage in Routes

```php
// Single role
Route::get('/admin/dashboard', function () {
    return 'Admin Dashboard';
})->middleware(['auth:sanctum', 'role:admin']);

// Multiple roles (any of them)
Route::get('/staff/dashboard', function () {
    return 'Staff Dashboard';
})->middleware(['auth:sanctum', 'role:admin,super-admin,tata-usaha']);

// Grouped routes
Route::middleware(['auth:sanctum', 'role:admin,super-admin'])->group(function () {
    Route::get('/admin/users', 'AdminController@users');
    Route::get('/admin/settings', 'AdminController@settings');
});
```

### Response when unauthorized (403)

```json
{
    "success": false,
    "message": "Unauthorized. Required role: admin, super-admin"
}
```

## Example Routes with Role Protection

**File:** `routes/api.php`

```php
// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes - Any authenticated user
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Admin only routes
Route::middleware(['auth:sanctum', 'role:admin,super-admin'])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'index']);
    Route::post('/admin/users', [AdminController::class, 'store']);
});

// Guru only routes
Route::middleware(['auth:sanctum', 'role:guru,kepala-sekolah'])->group(function () {
    Route::get('/guru/kelas', [GuruController::class, 'kelas']);
    Route::get('/guru/siswa', [GuruController::class, 'siswa']);
});

// Siswa only routes
Route::middleware(['auth:sanctum', 'role:siswa'])->group(function () {
    Route::get('/siswa/nilai', [SiswaController::class, 'nilai']);
    Route::get('/siswa/jadwal', [SiswaController::class, 'jadwal']);
});
```

## Testing Examples

### 1. Register as Siswa

**PowerShell:**
```powershell
$body = @{
    name='Siswa Test'
    email='siswa@example.com'
    password='password123'
    password_confirmation='password123'
    role='siswa'
} | ConvertTo-Json

Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/auth/register' `
    -Method POST -Body $body `
    -ContentType 'application/json' `
    -Headers @{Accept='application/json'}
```

**cURL:**
```bash
curl -X POST http://127.0.0.1:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Siswa Test",
    "email": "siswa@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "siswa"
  }'
```

### 2. Register as Guru

**PowerShell:**
```powershell
$body = @{
    name='Guru Test'
    email='guru@example.com'
    password='password123'
    password_confirmation='password123'
    role='guru'
} | ConvertTo-Json

Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/auth/register' `
    -Method POST -Body $body `
    -ContentType 'application/json' `
    -Headers @{Accept='application/json'}
```

### 3. Register as Admin

**PowerShell:**
```powershell
$body = @{
    name='Admin User'
    email='admin@example.com'
    password='password123'
    password_confirmation='password123'
    role='admin'
} | ConvertTo-Json

Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/auth/register' `
    -Method POST -Body $body `
    -ContentType 'application/json' `
    -Headers @{Accept='application/json'}
```

### 4. Register as Super Admin

**PowerShell:**
```powershell
$body = @{
    name='Super Admin'
    email='superadmin@example.com'
    password='password123'
    password_confirmation='password123'
    role='super-admin'
} | ConvertTo-Json

Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/auth/register' `
    -Method POST -Body $body `
    -ContentType 'application/json' `
    -Headers @{Accept='application/json'}
```

## Using in Controller

```php
use Illuminate\Http\Request;

class SomeController extends Controller
{
    public function someMethod(Request $request)
    {
        $user = $request->user();
        
        // Check specific role (each role is separate)
        if ($user->isAdmin()) {
            // Only admin logic (NOT including super-admin)
        }
        
        if ($user->isSuperAdmin()) {
            // Only super-admin logic
        }
        
        if ($user->isGuru()) {
            // Only guru logic
        }
        
        if ($user->isSiswa()) {
            // Only siswa logic
        }
        
        // If you need to check multiple roles
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            // Admin OR super-admin logic
        }
        
        if ($user->hasAnyRole(['guru', 'kepala-sekolah'])) {
            // Guru OR kepala-sekolah logic
        }
        
        // Get current role
        $role = $user->role;
        
        return response()->json([
            'user' => $user,
            'role' => $role,
            'is_admin' => $user->isAdmin(),
            'is_super_admin' => $user->isSuperAdmin()
        ]);
    }
}
```

## Role Hierarchy Suggestion

Untuk implementasi lebih lanjut, Anda bisa membuat hierarki role:

```
super-admin (highest)
    └── Can access everything
admin
    └── Can manage most features
yayasan
    └── Can view reports and financials
kepala-sekolah
    └── Can manage school operations
tata-usaha
    └── Can manage administrative tasks
guru
    └── Can manage classes and students
orang-tua
    └── Can view their children's data
siswa (lowest)
    └── Can view their own data
```

## Security Best Practices

1. **Never trust client-side role validation** - Always validate on server
2. **Use middleware for route protection** - Don't rely on manual checks
3. **Log role changes** - Implement audit logging for security
4. **Restrict role assignment** - Only admins should be able to assign roles
5. **Validate role in forms** - Use select dropdowns instead of text input

## Migration Commands

```bash
# Fresh migration (drop all tables and re-run)
php artisan migrate:fresh

# Rollback last migration
php artisan migrate:rollback

# Reset all migrations
php artisan migrate:reset

# Rollback and re-run all
php artisan migrate:refresh
```

## Seeding Default Users

You can create a seeder for default users:

```php
// database/seeders/UserSeeder.php
public function run()
{
    User::create([
        'name' => 'Super Admin',
        'email' => 'superadmin@school.com',
        'password' => Hash::make('password'),
        'role' => 'super-admin'
    ]);
    
    User::create([
        'name' => 'Admin',
        'email' => 'admin@school.com',
        'password' => Hash::make('password'),
        'role' => 'admin'
    ]);
}
```

Run seeder:
```bash
php artisan db:seed --class=UserSeeder
```

---

## Summary

✅ **8 Different Roles** available
✅ **Role validation** in register endpoint
✅ **Helper methods** in User model
✅ **Middleware** for route protection
✅ **Easy to extend** and customize
✅ **Tested and working**

The role system is now fully integrated and ready to use!
