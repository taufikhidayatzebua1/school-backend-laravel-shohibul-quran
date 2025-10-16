# API Security Best Practices

## ✅ Current Implementation (Already Good)

### 1. **Separation of Public and Protected Routes**
```php
// Public routes - /api/public/*
// Protected routes - /api/*
```
✅ **Good:** Clear separation memudahkan management dan security

### 2. **Role-Based Access Control (RBAC)**
```php
Route::middleware('role:guru,kepala-sekolah,admin,super-admin')
```
✅ **Good:** Granular access control based on user roles

### 3. **JSON Response for API Errors**
```php
// Returns JSON instead of redirect
return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
```
✅ **Good:** Consistent API responses

### 4. **Laravel Sanctum for Authentication**
✅ **Good:** Lightweight and secure token-based authentication

---

## 🔒 Recommended Security Improvements

### 1. **Rate Limiting (Wajib untuk Public API)**

#### A. Add Rate Limiting to Public Routes
Mencegah abuse dan DDoS attacks pada public endpoints.

**Implementation:**
```php
// routes/api.php
// Public routes dengan rate limiting
Route::prefix('public')->middleware('throttle:60,1')->group(function () {
    // 60 requests per minute per IP
    Route::prefix('hafalan')->group(function () {
        Route::get('/', [HafalanController::class, 'index']);
        // ... other routes
    });
});

// Untuk public auth endpoints (lebih ketat)
Route::prefix('auth')->middleware('throttle:10,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
});
```

**Benefits:**
- ✅ Mencegah brute force attacks pada login
- ✅ Mencegah spam pada registration
- ✅ Melindungi dari excessive API calls

#### B. Different Rate Limits for Authenticated Users
```php
// Protected routes dengan rate limit lebih tinggi
Route::middleware(['auth:sanctum', 'throttle:200,1'])->group(function () {
    // Authenticated users dapat 200 requests per minute
});
```

---

### 2. **CORS Configuration**

**File:** `config/cors.php`
```php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:8100',  // Ionic app
        'https://yourdomain.com', // Production domain
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

**Benefits:**
- ✅ Hanya domain tertentu yang bisa akses API
- ✅ Mencegah unauthorized cross-origin requests

---

### 3. **Input Validation & Sanitization**

#### A. Validate ALL Input (Already doing this ✅)
```php
$validator = Validator::make($request->all(), [
    'email' => 'required|email',
    'password' => 'required|min:8',
]);
```

#### B. Add More Specific Validation Rules
```php
// Example untuk HafalanController
$validator = Validator::make($request->all(), [
    'siswa_id' => 'required|exists:siswa,id',
    'juz' => 'required|integer|between:1,30',
    'surah' => 'required|string|max:50',
    'ayat_mulai' => 'required|integer|min:1',
    'ayat_selesai' => 'required|integer|min:1',
    'nilai' => 'required|numeric|between:0,100',
]);
```

**Benefits:**
- ✅ Mencegah SQL injection
- ✅ Mencegah invalid data entry
- ✅ Data integrity

---

### 4. **API Versioning**

```php
// routes/api.php
Route::prefix('v1')->group(function () {
    // Public routes
    Route::prefix('public')->group(function () {
        // ...
    });
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // ...
    });
});
```

**Access:** `/api/v1/public/siswa` atau `/api/v1/siswa`

**Benefits:**
- ✅ Backward compatibility
- ✅ Easier API evolution
- ✅ Can maintain multiple versions

---

### 5. **Query Parameter Validation**

Untuk public API, batasi parameters yang bisa digunakan:

```php
// SiswaController.php
public function index(Request $request)
{
    // Validate query parameters
    $validator = Validator::make($request->all(), [
        'page' => 'integer|min:1',
        'per_page' => 'integer|min:1|max:100', // Max 100 per page
        'kelas_id' => 'exists:kelas,id',
        'search' => 'string|max:100',
    ]);
    
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid query parameters',
            'errors' => $validator->errors()
        ], 422);
    }
    
    // Query dengan validated parameters
    $perPage = $request->get('per_page', 10);
    // ...
}
```

**Benefits:**
- ✅ Mencegah excessive data retrieval
- ✅ Validate user input
- ✅ Prevent performance issues

---

### 6. **Response Data Limiting (Public API)**

Jangan expose semua data di public API:

```php
// SiswaController.php (untuk public route)
public function index()
{
    $siswa = Siswa::with(['kelas:id,nama_kelas'])
        ->select('id', 'nis', 'nama', 'kelas_id') // Hanya field tertentu
        ->paginate(10);
    
    return response()->json([
        'success' => true,
        'message' => 'Data siswa berhasil diambil',
        'data' => $siswa
    ]);
}

// Untuk protected route, bisa return lebih banyak data
public function showForStaff($id)
{
    $siswa = Siswa::with(['user', 'kelas', 'hafalan'])
        ->findOrFail($id); // Include sensitive data
    
    return response()->json([
        'success' => true,
        'data' => $siswa
    ]);
}
```

---

### 7. **Security Headers**

**File:** `app/Http/Middleware/SecurityHeaders.php`
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        
        return $response;
    }
}
```

**Register in bootstrap/app.php:**
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
})
```

---

### 8. **API Key for Public Routes (Optional)**

Untuk public API yang lebih secure, gunakan API Key:

```php
// .env
PUBLIC_API_KEY=your-secret-api-key-here

// Middleware: CheckApiKey.php
public function handle(Request $request, Closure $next)
{
    $apiKey = $request->header('X-API-Key');
    
    if ($apiKey !== config('app.public_api_key')) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid API Key'
        ], 401);
    }
    
    return $next($request);
}

// Apply to public routes
Route::prefix('public')->middleware('api.key')->group(function () {
    // ...
});
```

**Benefits:**
- ✅ Additional layer of security
- ✅ Track API usage per key
- ✅ Can revoke keys if abused

---

### 9. **Logging & Monitoring**

```php
// Log suspicious activities
Log::channel('security')->warning('Failed login attempt', [
    'email' => $request->email,
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
]);

// Log API access
Log::channel('api')->info('API accessed', [
    'endpoint' => $request->path(),
    'method' => $request->method(),
    'user_id' => auth()->id(),
    'ip' => $request->ip(),
]);
```

---

### 10. **Token Expiration & Refresh**

```php
// config/sanctum.php
'expiration' => 60, // Token expires in 60 minutes

// AuthController.php
public function login(Request $request)
{
    // ...
    $token = $user->createToken('auth_token', ['*'], now()->addHours(24))->plainTextToken;
    
    return response()->json([
        'success' => true,
        'message' => 'Login successful',
        'data' => [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 86400, // seconds (24 hours)
        ]
    ]);
}
```

---

## 📋 Implementation Priority

### 🔴 **High Priority (Must Have)**
1. ✅ Rate Limiting on public routes (prevent abuse)
2. ✅ CORS configuration (prevent unauthorized access)
3. ✅ Input validation & sanitization (prevent SQL injection)
4. ✅ Security headers (prevent XSS, clickjacking)

### 🟡 **Medium Priority (Should Have)**
5. ✅ Response data limiting (don't expose sensitive data)
6. ✅ Query parameter validation (prevent performance issues)
7. ✅ Token expiration (prevent long-lived tokens)
8. ✅ Logging & monitoring (detect suspicious activities)

### 🟢 **Low Priority (Nice to Have)**
9. ✅ API versioning (for future updates)
10. ✅ API Key for public routes (if needed)

---

## 🎯 Current Architecture Summary

```
┌─────────────────────────────────────────────────────┐
│                   API STRUCTURE                     │
├─────────────────────────────────────────────────────┤
│                                                     │
│  PUBLIC API (No Auth)                              │
│  └─ /api/public/*                                  │
│     ├─ Rate Limited: 60 req/min (recommended)      │
│     ├─ Limited Data Response                       │
│     └─ Query Parameter Validation                  │
│                                                     │
│  AUTH API (No Auth Required)                       │
│  └─ /api/auth/login, /api/auth/register           │
│     ├─ Rate Limited: 10 req/min (recommended)      │
│     └─ Strong Validation                           │
│                                                     │
│  PROTECTED API (Auth Required)                     │
│  └─ /api/*                                         │
│     ├─ Middleware: auth:sanctum                    │
│     ├─ Role Check: guru, kepala-sekolah, admin    │
│     ├─ Rate Limited: 200 req/min (recommended)     │
│     └─ Full Data Access                            │
│                                                     │
└─────────────────────────────────────────────────────┘
```

---

## 🛡️ Security Checklist

- [x] Separate public and protected routes
- [x] Role-based access control (RBAC)
- [x] JSON error responses for API
- [x] Token-based authentication (Sanctum)
- [ ] **Rate limiting on public routes**
- [ ] **CORS configuration**
- [ ] **Security headers**
- [ ] Input validation & sanitization (partial)
- [ ] Response data limiting
- [ ] Query parameter validation
- [ ] Token expiration
- [ ] Logging & monitoring
- [ ] API versioning
- [ ] API key for public routes (optional)

---

## 📚 References

- [Laravel Security Best Practices](https://laravel.com/docs/security)
- [OWASP API Security Top 10](https://owasp.org/www-project-api-security/)
- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
- [Laravel Rate Limiting](https://laravel.com/docs/routing#rate-limiting)
