# API Routes Restructuring & Simplification

## Changes Made - 2025-10-20

Merestrukturisasi dan menyederhanakan routing API untuk meningkatkan maintainability, readability, dan security.

---

## 🔄 Key Changes

### 1. **Removed Redundant Role Middleware**
**BEFORE:**
```php
// Double nested middleware - redundant!
Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware('role:guru,kepala-sekolah,admin,super-admin')->group(function () {
        // All resources here
    });
});
```

**AFTER:**
```php
// Single auth middleware for all protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Resources organized by function
    // Role middleware only where needed (write operations)
});
```

### 2. **Better Route Organization**
Routes now organized by:
- ✅ **Security Level** (Public → Auth → Admin)
- ✅ **Resource Type** (Clear sections)
- ✅ **Access Pattern** (Read vs Write)

### 3. **Consistent Access Control Pattern**
```php
Route::prefix('resource')->group(function () {
    // Read Access (All authenticated users)
    Route::get('/', ...);
    Route::get('/{id}', ...);
    
    // Write Access (Admin only)
    Route::middleware('role:tata-usaha,admin,super-admin')->group(function () {
        Route::post('/', ...);
        Route::put('/{id}', ...);
        Route::delete('/{id}', ...);
    });
});
```

---

## 📋 New Route Structure

```
api/v1/
├── Public Routes (No Auth)
│   ├── auth/
│   │   ├── POST   /login
│   │   ├── POST   /forgot-password
│   │   └── POST   /reset-password
│   └── public/
│       ├── hafalan/
│       ├── kelas/
│       └── siswa/
│
└── Protected Routes (Auth Required)
    ├── auth/
    │   ├── POST   /logout
    │   ├── GET    /profile
    │   ├── PUT    /profile
    │   └── POST   /revoke-tokens
    │
    ├── users/ (Admin Only)
    │   ├── GET    /
    │   ├── POST   /
    │   └── ...
    │
    ├── tahun-ajaran/
    │   ├── GET    / (All Auth)
    │   ├── GET    /active (All Auth)
    │   ├── POST   / (Admin Only)
    │   └── ...
    │
    ├── kelas/
    │   ├── GET    / (All Auth)
    │   ├── GET    /{id} (All Auth)
    │   ├── POST   / (Admin Only)
    │   └── ...
    │
    ├── siswa/
    │   ├── GET    / (All Auth)
    │   ├── GET    /{id} (All Auth)
    │   ├── GET    /{id}/hafalan (All Auth)
    │   ├── POST   / (Admin Only)
    │   └── ...
    │
    ├── guru/
    │   ├── GET    / (All Auth)
    │   ├── GET    /{id} (All Auth)
    │   ├── POST   / (Admin Only)
    │   └── ...
    │
    ├── orang-tua/
    │   ├── GET    / (All Auth)
    │   ├── GET    /{id} (All Auth)
    │   ├── POST   / (Admin Only)
    │   └── ...
    │
    └── hafalan/
        ├── GET    / (All Auth)
        ├── GET    /{id} (All Auth)
        ├── GET    /statistics (All Auth)
        ├── POST   / (Guru/Admin)
        └── ...
```

---

## 🎯 Benefits

### 1. **Simplified Access Control**

**BEFORE:**
```php
// ❌ Redundant - All users inside already checked for role
Route::middleware('role:guru,kepala-sekolah,admin,super-admin')->group(function () {
    Route::get('/siswa', ...); // Only read, no need role check!
});
```

**AFTER:**
```php
// ✅ Only authenticated users (any role can read)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/siswa', ...); // Anyone logged in can view
    
    // Role check only for write operations
    Route::middleware('role:admin')->group(function () {
        Route::post('/siswa', ...);
    });
});
```

### 2. **Better Separation of Concerns**

| Level | Who Can Access | Examples |
|-------|----------------|----------|
| **Public** | Anyone | Login, Public Hafalan List |
| **Authenticated** | All logged in users | View Siswa, View Kelas, View Hafalan |
| **Admin** | Admin roles only | Create/Update/Delete Master Data |
| **Guru+** | Guru & Admin | Create/Update Hafalan |

### 3. **Improved Readability**

**BEFORE:**
```php
// Hard to see structure
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:...')->group(function () {
        Route::prefix('siswa')->group(function () {
            Route::get('/', ...);
            Route::middleware('role:...')->group(function () {
                Route::post('/', ...);
            });
        });
    });
});
```

**AFTER:**
```php
// Clear structure with comments
/*
|--------------------------------------------------------------------------
| Siswa Management
|--------------------------------------------------------------------------
*/
Route::prefix('siswa')->group(function () {
    // Read Access (All authenticated users)
    Route::get('/', ...);
    
    // Write Access (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::post('/', ...);
    });
});
```

### 4. **Consistent Pattern**

All resources follow same pattern:
1. ✅ **Section comment** for clarity
2. ✅ **Read routes first** (public access)
3. ✅ **Write routes nested** (restricted access)
4. ✅ **Clear role requirements** in comments

---

## 🔐 Access Control Matrix

### **Read Operations** (GET)
| Resource | Access Level | Roles |
|----------|-------------|-------|
| Public API | ❌ No Auth | Anyone |
| Tahun Ajaran | ✅ Auth | All logged in |
| Kelas | ✅ Auth | All logged in |
| Siswa | ✅ Auth | All logged in |
| Guru | ✅ Auth | All logged in |
| Orang Tua | ✅ Auth | All logged in |
| Hafalan | ✅ Auth | All logged in |
| Users | 🔒 Admin | Admin only |

### **Write Operations** (POST/PUT/DELETE)
| Resource | Access Level | Roles |
|----------|-------------|-------|
| Tahun Ajaran | 🔒 Admin | tata-usaha, admin, super-admin |
| Kelas | 🔒 Admin | tata-usaha, admin, super-admin |
| Siswa | 🔒 Admin | tata-usaha, admin, super-admin |
| Guru | 🔒 Admin | tata-usaha, admin, super-admin |
| Orang Tua | 🔒 Admin | tata-usaha, admin, super-admin |
| Hafalan | 🔓 Guru+ | guru, kepala-sekolah, tata-usaha, admin, super-admin |
| Users | 🔒 Admin | tata-usaha, admin, super-admin |

---

## 📝 Detailed Changes by Section

### **1. Public Routes (No Changes)**
```php
// Tetap sama - Public API untuk guest users
Route::prefix('public')->group(function () {
    // hafalan, kelas, siswa
});
```

### **2. Authentication Routes**
**BEFORE:**
```php
Route::prefix('auth')->group(function () {
    Route::post('/register', ...); // ❌ Protected tapi di public group
});
```

**AFTER:**
```php
// Public auth
Route::prefix('auth')->group(function () {
    Route::post('/login', ...);
    Route::post('/forgot-password', ...);
    Route::post('/reset-password', ...);
});

// Protected auth
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', ...);
        Route::get('/profile', ...);
    });
});
```

### **3. User Management**
```php
// ✅ No change - Already admin only
Route::prefix('users')->middleware('role:admin')->group(function () {
    // CRUD operations
});
```

### **4. Resource Management (Main Changes)**

**BEFORE:**
```php
// ❌ All wrapped in role middleware
Route::middleware('role:guru,kepala-sekolah,admin,super-admin')->group(function () {
    Route::prefix('tahun-ajaran')->group(function () {
        Route::get('/', ...); // ❌ Siswa tidak bisa akses
        Route::post('/', ...);
    });
});
```

**AFTER:**
```php
// ✅ Only auth required for reads
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('tahun-ajaran')->group(function () {
        // Anyone logged in can read
        Route::get('/', ...);
        
        // Only admin can write
        Route::middleware('role:admin')->group(function () {
            Route::post('/', ...);
        });
    });
});
```

### **5. Hafalan (Special Case)**
```php
// Hafalan needs Guru+ access for write
Route::prefix('hafalan')->group(function () {
    // Read: All authenticated
    Route::get('/', ...);
    
    // Write: Guru and Admin
    Route::middleware('role:guru,kepala-sekolah,tata-usaha,admin,super-admin')->group(function () {
        Route::post('/', ...);
        Route::put('/{id}', ...);
        Route::delete('/{id}', ...);
    });
});
```

---

## 🔍 Use Cases

### **Use Case 1: Siswa Login**
```bash
# Before: ❌ 403 Forbidden
GET /api/v1/siswa
# Error: Role 'siswa' not in [guru, kepala-sekolah, admin, super-admin]

# After: ✅ 200 OK
GET /api/v1/siswa
# Success: Siswa can view list (read-only)
```

### **Use Case 2: Guru Create Hafalan**
```bash
# Before: ✅ Allowed
POST /api/v1/hafalan

# After: ✅ Still Allowed
POST /api/v1/hafalan
# Guru still has write access to hafalan
```

### **Use Case 3: Orang Tua View Child Data**
```bash
# Before: ❌ 403 Forbidden
GET /api/v1/siswa/1

# After: ✅ 200 OK
GET /api/v1/siswa/1
# Orang tua can view their child's data
```

### **Use Case 4: Admin Operations**
```bash
# Before: ✅ Allowed
POST /api/v1/siswa

# After: ✅ Still Allowed
POST /api/v1/siswa
# Admin still has full access
```

---

## 🧪 Testing Checklist

### **Test Public Routes**
```bash
# No auth required
✓ POST /api/v1/auth/login
✓ GET  /api/v1/public/siswa
✓ GET  /api/v1/public/hafalan
```

### **Test Authenticated Read Access (All Roles)**
```bash
# Login as: siswa, orang-tua, guru, admin
✓ GET  /api/v1/siswa
✓ GET  /api/v1/guru
✓ GET  /api/v1/kelas
✓ GET  /api/v1/hafalan
✓ GET  /api/v1/tahun-ajaran
```

### **Test Admin Write Access**
```bash
# Login as: tata-usaha, admin, super-admin
✓ POST   /api/v1/siswa
✓ PUT    /api/v1/siswa/1
✓ DELETE /api/v1/siswa/1
✓ POST   /api/v1/kelas
```

### **Test Guru Write Access**
```bash
# Login as: guru
✓ POST   /api/v1/hafalan
✓ PUT    /api/v1/hafalan/1
✗ POST   /api/v1/siswa (403 - Admin only)
```

### **Test Unauthorized Access**
```bash
# Login as: siswa
✗ POST   /api/v1/siswa (403 - Admin only)
✗ POST   /api/v1/hafalan (403 - Guru+ only)
✗ DELETE /api/v1/kelas/1 (403 - Admin only)
```

---

## 📚 Documentation Updates

### **API Permissions Summary**

| Endpoint | Method | Public | Siswa | Orang Tua | Guru | Admin |
|----------|--------|--------|-------|-----------|------|-------|
| `/public/*` | GET | ✅ | ✅ | ✅ | ✅ | ✅ |
| `/auth/login` | POST | ✅ | ✅ | ✅ | ✅ | ✅ |
| `/auth/profile` | GET | ❌ | ✅ | ✅ | ✅ | ✅ |
| `/siswa` | GET | ❌ | ✅ | ✅ | ✅ | ✅ |
| `/siswa` | POST | ❌ | ❌ | ❌ | ❌ | ✅ |
| `/guru` | GET | ❌ | ✅ | ✅ | ✅ | ✅ |
| `/guru` | POST | ❌ | ❌ | ❌ | ❌ | ✅ |
| `/hafalan` | GET | ❌ | ✅ | ✅ | ✅ | ✅ |
| `/hafalan` | POST | ❌ | ❌ | ❌ | ✅ | ✅ |
| `/users` | * | ❌ | ❌ | ❌ | ❌ | ✅ |

---

## 🎨 Code Quality Improvements

### **1. Better Comments**
```php
/*
|--------------------------------------------------------------------------
| Siswa Management
|--------------------------------------------------------------------------
*/
```

### **2. Grouped by Function**
- Authentication & Profile
- User Management
- Master Data (Tahun Ajaran, Kelas)
- User Data (Siswa, Guru, Orang Tua)
- Operational Data (Hafalan)

### **3. Consistent Indentation**
All route groups properly indented and organized

### **4. Clear Access Patterns**
```php
// Read Access (All authenticated users)
// Write Access (Admin only)
```

---

## ⚠️ Breaking Changes

### **For Siswa & Orang Tua Users**
✅ **NOW ALLOWED:**
- View siswa list
- View guru list
- View kelas list
- View hafalan records
- View tahun ajaran

❌ **Still NOT ALLOWED:**
- Create/Update/Delete any data

### **For Guru Users**
✅ **NOW ALLOWED:**
- All read operations (same as siswa)
- Create/Update/Delete hafalan (same as before)

❌ **Still NOT ALLOWED:**
- Create/Update/Delete master data

### **For Admin Users**
✅ **No changes** - Still has full access

---

## 🔄 Migration Guide

### **Frontend Changes Required:**

**No breaking changes for existing functionality!**

All endpoints remain the same, only access permissions expanded:

```javascript
// ✅ This now works for all authenticated users (was guru+ only)
GET /api/v1/siswa
GET /api/v1/kelas
GET /api/v1/hafalan

// ✅ Still admin only (no change)
POST /api/v1/siswa
PUT /api/v1/siswa/1
DELETE /api/v1/siswa/1
```

---

**Date:** 2025-10-20  
**Type:** Route Restructuring / Access Control Improvement  
**Impact:** Non-breaking - Expands read access to all authenticated users  
**Benefits:** Better UX, More intuitive permissions, Cleaner code structure
