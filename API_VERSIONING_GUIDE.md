# API Versioning Implementation Guide

## ✅ **Implementasi Selesai!**

API Anda sekarang menggunakan versioning dengan struktur:
```
/api/v1/*
```

---

## 📋 **URL Structure**

### **Before (Without Versioning):**
```
/api/auth/login
/api/public/siswa
/api/siswa
```

### **After (With Versioning):**
```
/api/v1/auth/login
/api/v1/public/siswa
/api/v1/siswa
```

---

## 🎯 **Kenapa Versioning Penting?**

### **1. Backward Compatibility**
Ketika Anda ingin mengubah API (breaking changes), v1 masih bisa berjalan untuk client lama:
```php
// v1 - Legacy (still works)
GET /api/v1/siswa
Response: { id, nis, nama, kelas_id, ... }

// v2 - New version (with changes)
GET /api/v2/siswa  
Response: { uuid, student_number, full_name, class: {...}, ... }
```

### **2. Gradual Migration**
Client apps bisa migrate bertahap:
- Mobile app v1.0 → uses `/api/v1`
- Mobile app v2.0 → uses `/api/v2`
- Web app → still uses `/api/v1`

### **3. Clear Communication**
Dokumentasi lebih jelas:
- `/api/v1` → Stable, documented
- `/api/v2` → New features
- `/api/v1` deprecated but still working

---

## 📊 **Current API Endpoints**

### **Health Check** (No version required)
```
GET /api/health

Response:
{
  "status": "ok",
  "version": "v1",
  "timestamp": "2025-10-16T13:42:18+00:00"
}
```

### **V1 Public Routes** (No Auth)
```
GET  /api/v1/public/siswa
GET  /api/v1/public/siswa/{id}
GET  /api/v1/public/siswa/{id}/hafalan
GET  /api/v1/public/siswa/{id}/statistics

GET  /api/v1/public/kelas
GET  /api/v1/public/kelas/{id}
GET  /api/v1/public/kelas/{id}/siswa

GET  /api/v1/public/hafalan
POST /api/v1/public/hafalan
GET  /api/v1/public/hafalan/{id}
PUT  /api/v1/public/hafalan/{id}
DELETE /api/v1/public/hafalan/{id}
GET  /api/v1/public/hafalan/statistics
```

### **V1 Auth Routes** (No Auth Required)
```
POST /api/v1/auth/register
POST /api/v1/auth/login
POST /api/v1/auth/forgot-password
POST /api/v1/auth/reset-password
```

### **V1 Protected Routes** (Auth Required)
```
POST /api/v1/auth/logout
GET  /api/v1/auth/profile
PUT  /api/v1/auth/profile
POST /api/v1/auth/revoke-tokens

GET  /api/v1/siswa (requires role)
GET  /api/v1/siswa/{id}
GET  /api/v1/siswa/{id}/hafalan
GET  /api/v1/siswa/{id}/statistics

GET  /api/v1/kelas
GET  /api/v1/kelas/{id}
GET  /api/v1/kelas/{id}/siswa

GET  /api/v1/hafalan
POST /api/v1/hafalan
GET  /api/v1/hafalan/{id}
PUT  /api/v1/hafalan/{id}
DELETE /api/v1/hafalan/{id}
GET  /api/v1/hafalan/statistics
```

---

## 🚀 **Cara Membuat Version 2 (Future)**

Ketika Anda butuh breaking changes, buat v2:

### **Step 1: Copy Routes**
```php
// routes/api.php

// V1 - Keep existing
Route::prefix('v1')->group(function () {
    // ... existing routes
});

// V2 - New version with changes
Route::prefix('v2')->group(function () {
    Route::prefix('auth')->middleware('throttle:10,1')->group(function () {
        Route::post('/login', [AuthControllerV2::class, 'login']);
        // ... new implementation
    });
    // ... other routes with changes
});
```

### **Step 2: Create New Controllers (if needed)**
```php
// app/Http/Controllers/Api/V2/AuthController.php
namespace App\Http\Controllers\Api\V2;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // New implementation with breaking changes
        // e.g., return different response structure
    }
}
```

### **Step 3: Update Documentation**
```markdown
## API v1 (Deprecated - Will be removed on 2026-01-01)
Still working but use v2 for new apps

## API v2 (Current)
New features and improvements
```

### **Step 4: Deprecation Notice**
```php
// V1 routes (add deprecation header)
Route::prefix('v1')->group(function () {
    Route::get('/siswa', function () {
        return response()->json([...])
            ->header('X-API-Deprecation', 'This endpoint is deprecated. Use /api/v2/siswa')
            ->header('X-API-Sunset', '2026-01-01');
    });
});
```

---

## 🎨 **Best Practices**

### ✅ **DO:**
1. ✅ Use URL path versioning (`/api/v1/`)
2. ✅ Keep old versions working (backward compatibility)
3. ✅ Document version differences clearly
4. ✅ Set deprecation timeline (6-12 months)
5. ✅ Increment version only for breaking changes
6. ✅ Use semantic versioning concept (v1, v2, v3)

### ❌ **DON'T:**
1. ❌ Remove old versions without notice
2. ❌ Make breaking changes in same version
3. ❌ Create new version for minor changes
4. ❌ Use query parameters for versioning (`?version=1`)
5. ❌ Force immediate migration
6. ❌ Use date-based versioning (`/api/2025-10/`)

---

## 📈 **Version Strategy**

### **When to Create New Version?**

#### **Breaking Changes (Need New Version):**
- ✅ Changed response structure
- ✅ Removed fields from response
- ✅ Changed data types
- ✅ Changed authentication method
- ✅ Removed endpoints

**Example:**
```php
// V1
GET /api/v1/siswa/{id}
Response: {
  "id": 1,
  "nama": "Andi"
}

// V2 - BREAKING CHANGE (renamed field)
GET /api/v2/siswa/{id}
Response: {
  "id": 1,
  "full_name": "Andi"  // renamed from 'nama'
}
```

#### **Non-Breaking Changes (Same Version):**
- ✅ Added new optional fields
- ✅ Added new endpoints
- ✅ Added new query parameters
- ✅ Improved performance
- ✅ Fixed bugs

**Example:**
```php
// V1 - Original
GET /api/v1/siswa/{id}
Response: {
  "id": 1,
  "nama": "Andi"
}

// V1 - Added new field (non-breaking)
GET /api/v1/siswa/{id}
Response: {
  "id": 1,
  "nama": "Andi",
  "email": "andi@test.com"  // NEW optional field
}
```

---

## 🧪 **Testing Versioned API**

### **Test Script Updated:**
```php
// All test scripts now use v1
$baseUrl = 'http://127.0.0.1:8000/api/v1';

// Test public route
GET /api/v1/public/siswa ✅

// Test auth
POST /api/v1/auth/login ✅

// Test protected
GET /api/v1/siswa ✅
```

### **Test Results:**
```
✅ Public Route: /api/v1/public/siswa (200 OK)
✅ Auth Route: /api/v1/auth/login (200 OK)
✅ Protected Route: /api/v1/siswa (401 without token)
✅ Health Check: /api/health (200 OK)
```

---

## 📦 **Client Implementation**

### **Ionic/Angular:**
```typescript
// environment.ts
export const environment = {
  apiUrl: 'http://localhost:8000/api/v1',
  apiVersion: 'v1'
};

// api.service.ts
const API_URL = environment.apiUrl;

getSiswa() {
  return this.http.get(`${API_URL}/public/siswa`);
}

login(credentials) {
  return this.http.post(`${API_URL}/auth/login`, credentials);
}
```

### **React/Next.js:**
```javascript
// config.js
export const API_CONFIG = {
  baseUrl: 'http://localhost:8000/api/v1',
  version: 'v1'
};

// api.js
const API_URL = API_CONFIG.baseUrl;

export const getSiswa = async () => {
  const response = await fetch(`${API_URL}/public/siswa`);
  return response.json();
};
```

---

## 🎯 **Migration Plan (When Creating V2)**

### **Phase 1: Announce (Month 1)**
```
- Announce v2 is coming
- Document all changes
- Provide migration guide
```

### **Phase 2: Beta (Month 2-3)**
```
- Release v2 as beta
- v1 still fully supported
- Encourage testing
```

### **Phase 3: Stable (Month 4-6)**
```
- v2 becomes stable
- v1 marked as deprecated
- Set sunset date (6-12 months)
```

### **Phase 4: Deprecation (Month 7-12)**
```
- Add deprecation headers to v1
- Send notices to clients
- Monitor v1 usage
```

### **Phase 5: Sunset (Month 13+)**
```
- v1 returns 410 Gone
- All clients should use v2
- Remove v1 code
```

---

## 📝 **Documentation Template**

### **API v1 Documentation:**
```markdown
# API v1 Documentation

## Status
✅ Current and Stable

## Base URL
`https://api.yourdomain.com/v1`

## Authentication
Bearer Token (Sanctum)

## Rate Limits
- Public: 60 req/min
- Auth: 10 req/min
- Protected: 200 req/min

## Endpoints
[List all endpoints...]
```

---

## 🎉 **Summary**

### ✅ **Implemented:**
- [x] URL path versioning (`/api/v1`)
- [x] All routes under v1 prefix
- [x] Health check endpoint
- [x] Backward compatible structure
- [x] Ready for future versions

### 🎯 **Benefits:**
1. ✅ Can add v2 without breaking v1
2. ✅ Clear communication of API version
3. ✅ Professional API design
4. ✅ Easy to maintain multiple versions
5. ✅ Client apps won't break on updates

### 📊 **Current Structure:**
```
/api/
├── health (version info)
└── v1/
    ├── auth/* (authentication)
    ├── public/* (no auth required)
    ├── siswa/* (protected)
    ├── kelas/* (protected)
    └── hafalan/* (protected)
```

---

## 💡 **Recommendation:**

**Kapan membuat v2?**
- Ketika ada perubahan struktur database yang signifikan
- Ketika ingin mengubah response format secara drastis
- Ketika ada breaking changes yang tidak bisa dihindari
- Minimal 6-12 bulan dari v1 stable

**Untuk sekarang:**
- ✅ Gunakan v1 untuk development
- ✅ Fokus pada fitur dan stabilitas
- ✅ Dokumentasikan dengan baik
- ✅ Monitor usage dan feedback
