# API Authentication Documentation

Base URL: `http://localhost:8000/api`

## Endpoints

### 1. Register
**Endpoint:** `POST /auth/register`

**Description:** Register a new user account

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "siswa"
}
```

**Available Roles:**
- `siswa` (default) - Siswa/pelajar
- `orang-tua` - Orang tua siswa
- `guru` - Guru/pengajar
- `kepala-sekolah` - Kepala sekolah
- `tata-usaha` - Staff tata usaha
- `yayasan` - Pihak yayasan
- `admin` - Administrator
- `super-admin` - Super administrator

**Response Success (201):**
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

**Response Error (422):**
```json
{
    "success": false,
    "message": "Validation errors",
    "errors": {
        "email": ["The email has already been taken."]
    }
}
```

---

### 2. Login
**Endpoint:** `POST /auth/login`

**Description:** Login to existing account

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2025-10-15T10:00:00.000000Z",
            "updated_at": "2025-10-15T10:00:00.000000Z"
        },
        "access_token": "2|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
        "token_type": "Bearer"
    }
}
```

**Response Error (401):**
```json
{
    "success": false,
    "message": "Invalid login credentials"
}
```

---

### 3. Get Profile
**Endpoint:** `GET /auth/profile`

**Description:** Get authenticated user profile

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "User profile retrieved successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-10-15T10:00:00.000000Z",
        "updated_at": "2025-10-15T10:00:00.000000Z"
    }
}
```

---

### 4. Update Profile
**Endpoint:** `PUT /auth/profile`

**Description:** Update user profile (name, email, or password)

**Headers:**
```
Authorization: Bearer {access_token}
```

**Request Body (Update name/email):**
```json
{
    "name": "John Updated",
    "email": "johnupdated@example.com"
}
```

**Request Body (Update password):**
```json
{
    "current_password": "password123",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Profile updated successfully",
    "data": {
        "id": 1,
        "name": "John Updated",
        "email": "johnupdated@example.com",
        "created_at": "2025-10-15T10:00:00.000000Z",
        "updated_at": "2025-10-15T10:30:00.000000Z"
    }
}
```

---

### 5. Forgot Password
**Endpoint:** `POST /auth/forgot-password`

**Description:** Send password reset link to user's email

**Request Body:**
```json
{
    "email": "john@example.com"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Password reset link sent to your email"
}
```

**Response Error (422):**
```json
{
    "success": false,
    "message": "Validation errors",
    "errors": {
        "email": ["The selected email is invalid."]
    }
}
```

---

### 6. Reset Password
**Endpoint:** `POST /auth/reset-password`

**Description:** Reset password using token from email

**Request Body:**
```json
{
    "token": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "email": "john@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Password has been reset successfully"
}
```

**Response Error (500):**
```json
{
    "success": false,
    "message": "This password reset token is invalid."
}
```

---

### 7. Logout
**Endpoint:** `POST /auth/logout`

**Description:** Logout and revoke current access token

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

---

### 8. Revoke All Tokens
**Endpoint:** `POST /auth/revoke-tokens`

**Description:** Revoke all access tokens for the user (logout from all devices)

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "All tokens revoked successfully"
}
```

---

## How to Use

### 1. Register or Login
First, register a new account or login to get an access token.

**cURL Example:**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### 2. Use Access Token
Save the `access_token` from the response and use it in the Authorization header for protected endpoints.

**cURL Example:**
```bash
curl -X GET http://localhost:8000/api/auth/profile \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -H "Accept: application/json"
```

### 3. Forgot Password Flow
1. User requests password reset:
```bash
curl -X POST http://localhost:8000/api/auth/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email": "john@example.com"}'
```

2. User receives email with reset link containing token
3. User submits new password with token:
```bash
curl -X POST http://localhost:8000/api/auth/reset-password \
  -H "Content-Type: application/json" \
  -d '{
    "token": "token-from-email",
    "email": "john@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
  }'
```

---

## Error Responses

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```

### 422 Validation Error
```json
{
    "success": false,
    "message": "Validation errors",
    "errors": {
        "field_name": ["Error message"]
    }
}
```

### 500 Server Error
```json
{
    "success": false,
    "message": "Error message"
}
```

---

## Notes

1. All timestamps are in UTC format
2. Access tokens don't expire by default in Sanctum
3. For production, consider adding rate limiting
4. CORS is enabled by default in Laravel
5. Email configuration is set to use SMTP (mail.livezet.id)
