# API Error Handling Documentation

## ✅ **Problem Solved!**

API sekarang mengembalikan **JSON response** untuk semua error, bukan HTML page.

---

## 🎯 **Error Response Format**

Semua error API mengikuti struktur yang konsisten:

```json
{
  "success": false,
  "message": "Error description",
  "additional_field": "optional data"
}
```

---

## 📊 **HTTP Status Codes & Responses**

### **200 OK** - Success
```json
{
  "success": true,
  "message": "Data siswa berhasil diambil",
  "data": { ... }
}
```

### **401 Unauthorized** - Not Authenticated
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

**Triggered when:**
- No authentication token provided
- Invalid token
- Expired token

**Example:**
```bash
GET /api/v1/siswa (without Authorization header)
```

---

### **403 Forbidden** - Wrong Role
```json
{
  "success": false,
  "message": "Unauthorized. Required role: guru, kepala-sekolah, admin, super-admin"
}
```

**Triggered when:**
- User authenticated but doesn't have required role
- Role-based access control denies access

**Example:**
```bash
GET /api/v1/siswa (with siswa role token)
```

---

### **404 Not Found** - Endpoint Doesn't Exist
```json
{
  "success": false,
  "message": "Endpoint not found.",
  "path": "api/v1/invalid-endpoint"
}
```

**Triggered when:**
- Invalid URL/endpoint
- Typo in endpoint path
- Resource doesn't exist

**Example:**
```bash
GET /api/v1/invalid-endpoint
GET /api/v1/siswa/999999 (ID doesn't exist)
```

---

### **405 Method Not Allowed** - Wrong HTTP Method
```json
{
  "success": false,
  "message": "Method not allowed.",
  "allowed_methods": "GET, HEAD"
}
```

**Triggered when:**
- Using POST on GET-only endpoint
- Using GET on POST-only endpoint
- Using wrong HTTP verb

**Example:**
```bash
POST /api/v1/public/siswa (should be GET)
GET /api/v1/auth/login (should be POST)
```

---

### **422 Unprocessable Entity** - Validation Failed
```json
{
  "success": false,
  "message": "Validation errors",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password field is required.",
      "The password must be at least 8 characters."
    ]
  }
}
```

**Triggered when:**
- Missing required fields
- Invalid data format
- Validation rules not met

**Example:**
```bash
POST /api/v1/auth/login
Body: {} (empty)
```

---

### **429 Too Many Requests** - Rate Limit Exceeded
```json
{
  "message": "Too Many Attempts.",
  "exception": "Illuminate\\Http\\Exceptions\\ThrottleRequestsException"
}
```

**Response Headers:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 0
Retry-After: 60
```

**Triggered when:**
- Exceeded rate limit (60 req/min for public, 10 for auth)
- Too many requests in short time

**Example:**
```bash
# After 60 requests in 1 minute
GET /api/v1/public/siswa
```

---

### **500 Internal Server Error** - Server Error

**Development Mode:**
```json
{
  "success": false,
  "message": "Actual error message",
  "debug": {
    "exception": "ErrorException",
    "file": "/path/to/file.php",
    "line": 42,
    "trace": [...]
  }
}
```

**Production Mode:**
```json
{
  "success": false,
  "message": "Internal server error."
}
```

**Triggered when:**
- Unhandled exceptions
- Database errors
- Code bugs

---

## 🔧 **Implementation**

### **File:** `bootstrap/app.php`

```php
->withExceptions(function (Exceptions $exceptions): void {
    
    // 401 - Unauthenticated
    $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }
    });
    
    // 404 - Not Found
    $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint not found.',
                'path' => $request->path()
            ], 404);
        }
    });
    
    // 405 - Method Not Allowed
    $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Method not allowed.',
                'allowed_methods' => $e->getHeaders()['Allow'] ?? 'N/A'
            ], 405);
        }
    });
    
    // 422 - Validation Error
    $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }
    });
    
    // 500 - General Exception
    $exceptions->render(function (\Throwable $e, $request) {
        if ($request->is('api/*')) {
            $message = config('app.debug') 
                ? $e->getMessage() 
                : 'Internal server error.';
            
            $response = [
                'success' => false,
                'message' => $message
            ];
            
            if (config('app.debug')) {
                $response['debug'] = [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => collect($e->getTrace())->take(5)->toArray()
                ];
            }
            
            return response()->json($response, 500);
        }
    });
});
```

---

## 🧪 **Test Results**

```
✅ 404 Not Found        → Returns JSON (not HTML)
✅ 405 Method Not Allowed → Returns JSON with allowed methods
✅ 401 Unauthorized      → Returns JSON (not redirect)
✅ 422 Validation Error  → Returns JSON with error details
✅ 200 Success          → Returns JSON with data
```

---

## 📱 **Client Implementation**

### **Handle Errors Consistently:**

```typescript
// Angular/Ionic
async getSiswa() {
  try {
    const response = await this.http.get('/api/v1/public/siswa').toPromise();
    if (response.success) {
      return response.data;
    }
  } catch (error) {
    // All errors are JSON
    console.error(error.error.message);
    
    switch (error.status) {
      case 401:
        // Redirect to login
        this.router.navigate(['/login']);
        break;
      case 403:
        // Show forbidden message
        this.showError('Access denied');
        break;
      case 404:
        // Show not found
        this.showError('Data not found');
        break;
      case 422:
        // Show validation errors
        this.showValidationErrors(error.error.errors);
        break;
      case 429:
        // Rate limit exceeded
        this.showError('Too many requests. Please try again later.');
        break;
      case 500:
        // Server error
        this.showError('Server error. Please contact support.');
        break;
    }
  }
}
```

---

## 🎨 **Best Practices**

### ✅ **DO:**
1. ✅ Always return JSON for API endpoints
2. ✅ Use consistent error structure
3. ✅ Include helpful error messages
4. ✅ Use proper HTTP status codes
5. ✅ Hide sensitive info in production
6. ✅ Log errors for debugging

### ❌ **DON'T:**
1. ❌ Return HTML for API errors
2. ❌ Expose stack traces in production
3. ❌ Use generic "error" messages
4. ❌ Return wrong status codes
5. ❌ Include sensitive data in errors
6. ❌ Ignore error logging

---

## 🔍 **Debugging**

### **Enable Debug Mode** (Development Only)
```env
# .env
APP_DEBUG=true
```

**With debug enabled:**
- Detailed error messages
- Stack traces
- File and line numbers

**Without debug (Production):**
- Generic error messages
- No sensitive information
- Cleaner responses

---

## 📊 **Error Response Summary**

| Status | Type | Returns JSON | Consistent Format |
|--------|------|--------------|-------------------|
| 200 | Success | ✅ | ✅ |
| 401 | Unauthenticated | ✅ | ✅ |
| 403 | Forbidden | ✅ | ✅ |
| 404 | Not Found | ✅ | ✅ |
| 405 | Method Not Allowed | ✅ | ✅ |
| 422 | Validation Error | ✅ | ✅ |
| 429 | Rate Limit | ✅ | ✅ |
| 500 | Server Error | ✅ | ✅ |

---

## 🎉 **Problem Solved!**

### **Before:**
```
GET /api/v1/invalid
Response: <!DOCTYPE html>... (HTML page)
```

### **After:**
```
GET /api/v1/invalid
Response: {"success":false,"message":"Endpoint not found.","path":"api/v1/invalid"}
```

**All API errors now return proper JSON responses! ✅**
