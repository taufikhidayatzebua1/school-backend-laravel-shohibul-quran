# Role Comparison Examples

## Perbedaan Antara Role-Specific Methods dan hasAnyRole()

### ❌ SEBELUM (Admin termasuk Super-Admin)
```php
// Method lama - isAdmin() termasuk super-admin
if ($user->isAdmin()) {
    // Ini akan true untuk role 'admin' DAN 'super-admin'
}
```

### ✅ SEKARANG (Setiap Role Terpisah)
```php
// Method baru - Setiap role terpisah
if ($user->isAdmin()) {
    // Ini HANYA true untuk role 'admin'
    // TIDAK termasuk 'super-admin'
}

if ($user->isSuperAdmin()) {
    // Ini HANYA true untuk role 'super-admin'
}

// Jika ingin check keduanya, gunakan hasAnyRole()
if ($user->hasAnyRole(['admin', 'super-admin'])) {
    // Ini true untuk 'admin' ATAU 'super-admin'
}
```

## Contoh Penggunaan di Controller

### 1. Check Role Spesifik
```php
public function adminOnlyAction(Request $request)
{
    $user = $request->user();
    
    if (!$user->isAdmin()) {
        return response()->json([
            'success' => false,
            'message' => 'Only admin can access this'
        ], 403);
    }
    
    // Admin logic here
}
```

### 2. Check Multiple Roles
```php
public function staffAction(Request $request)
{
    $user = $request->user();
    
    // Check if user is admin OR super-admin OR tata-usaha
    if (!$user->hasAnyRole(['admin', 'super-admin', 'tata-usaha'])) {
        return response()->json([
            'success' => false,
            'message' => 'Staff only'
        ], 403);
    }
    
    // Staff logic here
}
```

### 3. Different Logic per Role
```php
public function dashboard(Request $request)
{
    $user = $request->user();
    
    if ($user->isSuperAdmin()) {
        return $this->superAdminDashboard();
    }
    
    if ($user->isAdmin()) {
        return $this->adminDashboard();
    }
    
    if ($user->isGuru()) {
        return $this->guruDashboard();
    }
    
    if ($user->isSiswa()) {
        return $this->siswaDashboard();
    }
    
    // Default dashboard
    return $this->defaultDashboard();
}
```

## Route Protection Examples

### 1. Single Role Only
```php
// Only admin (NOT super-admin)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/reports', [AdminController::class, 'reports']);
});

// Only super-admin
Route::middleware(['auth:sanctum', 'role:super-admin'])->group(function () {
    Route::get('/superadmin/system', [SuperAdminController::class, 'system']);
});
```

### 2. Multiple Roles
```php
// Admin OR super-admin
Route::middleware(['auth:sanctum', 'role:admin,super-admin'])->group(function () {
    Route::get('/management/users', [ManagementController::class, 'users']);
});

// Guru OR kepala-sekolah
Route::middleware(['auth:sanctum', 'role:guru,kepala-sekolah'])->group(function () {
    Route::get('/teaching/classes', [TeachingController::class, 'classes']);
});
```

## All Available Methods

```php
// General methods
$user->hasRole('admin');                    // Check specific role
$user->hasAnyRole(['admin', 'guru']);      // Check multiple roles

// Role-specific methods (each is separate)
$user->isSiswa();           // Only 'siswa'
$user->isOrangTua();        // Only 'orang-tua'
$user->isGuru();            // Only 'guru'
$user->isKepalaSekolah();   // Only 'kepala-sekolah'
$user->isTataUsaha();       // Only 'tata-usaha'
$user->isYayasan();         // Only 'yayasan'
$user->isAdmin();           // Only 'admin' (NOT super-admin)
$user->isSuperAdmin();      // Only 'super-admin' (NOT admin)
```

## Testing Results

```
✅ User dengan role 'admin':
   isAdmin() = true
   isSuperAdmin() = false

✅ User dengan role 'super-admin':
   isAdmin() = false
   isSuperAdmin() = true

✅ User dengan role 'guru':
   isGuru() = true
   isAdmin() = false
   hasAnyRole(['guru', 'kepala-sekolah']) = true

✅ User dengan role 'siswa':
   isSiswa() = true
   isAdmin() = false
```

## Best Practices

### ✅ DO:
```php
// Gunakan role-specific method untuk check role tunggal
if ($user->isAdmin()) { }
if ($user->isSuperAdmin()) { }

// Gunakan hasAnyRole() untuk check multiple roles
if ($user->hasAnyRole(['admin', 'super-admin'])) { }

// Gunakan middleware untuk route protection
Route::middleware(['auth:sanctum', 'role:admin'])->group(...);
```

### ❌ DON'T:
```php
// Jangan assume isAdmin() termasuk super-admin
if ($user->isAdmin()) {
    // Ini TIDAK termasuk super-admin!
}

// Jangan manual check role string jika ada method
if ($user->role === 'admin') { }  // ❌ Gunakan $user->isAdmin() ✅

// Jangan lupa middleware untuk sensitive routes
Route::get('/admin/delete-all', ...);  // ❌ Tambahkan middleware! ✅
```

## Summary

✅ **Setiap role sekarang terpisah**
✅ **`isAdmin()` hanya check role `admin`**, tidak termasuk `super-admin`
✅ **`isSuperAdmin()` hanya check role `super-admin`**, tidak termasuk `admin`
✅ **Gunakan `hasAnyRole()` jika perlu check multiple roles**
✅ **Semua 8 role memiliki method sendiri**
✅ **Tested dan working dengan benar**
