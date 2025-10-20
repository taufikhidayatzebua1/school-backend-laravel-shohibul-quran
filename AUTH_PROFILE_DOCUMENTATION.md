# Auth Profile Endpoint Documentation

## Endpoint: `GET /api/v1/auth/profile`

Endpoint ini digunakan untuk mendapatkan informasi lengkap profil user yang sedang login, termasuk data spesifik berdasarkan role.

### Authentication
- **Required**: Yes
- **Type**: Bearer Token (Sanctum)

### Request Headers
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

### Response Structure

Response akan berbeda-beda tergantung pada role user:

#### 1. Role: `siswa`
User dengan role siswa akan mendapatkan informasi dari tabel `users` dan `siswa` beserta relasinya dengan `kelas`.

**Response Example:**
```json
{
    "success": true,
    "message": "User profile retrieved successfully",
    "data": {
        "id": 1,
        "name": "Ahmad Fauzi",
        "username": "ahmad_fauzi",
        "email": "siswa@example.com",
        "email_verified_at": null,
        "role": "siswa",
        "is_active": true,
        "created_at": "2024-10-20T10:30:00.000000Z",
        "updated_at": "2024-10-20T10:30:00.000000Z",
        "siswa": {
            "id": 1,
            "nis": "2024001",
            "nama": "Ahmad Fauzi",
            "jenis_kelamin": "L",
            "jenis_kelamin_text": "Laki-laki",
            "tempat_lahir": "Jakarta",
            "tanggal_lahir": "2010-05-15",
            "alamat": "Jl. Merdeka No. 123, Jakarta",
            "no_hp": "081234567890",
            "tahun_masuk": "2024",
            "url_photo": "https://example.com/photos/siswa1.jpg",
            "url_cover": "https://example.com/covers/siswa1.jpg",
            "is_active": true,
            "kelas": {
                "id": 1,
                "nama": "1A",
                "ruangan": "R101",
                "tingkat": 1
            }
        }
    }
}
```

#### 2. Role: `orang-tua`
User dengan role orang-tua akan mendapatkan informasi dari tabel `users` dan `orang_tua`.

**Response Example:**
```json
{
    "success": true,
    "message": "User profile retrieved successfully",
    "data": {
        "id": 2,
        "name": "Budi Santoso",
        "username": "budi_santoso",
        "email": "orangtua@example.com",
        "email_verified_at": null,
        "role": "orang-tua",
        "is_active": true,
        "created_at": "2024-10-20T10:30:00.000000Z",
        "updated_at": "2024-10-20T10:30:00.000000Z",
        "orang_tua": {
            "id": 1,
            "nama": "Budi Santoso",
            "jenis_kelamin": "L",
            "jenis_kelamin_text": "Laki-laki",
            "tempat_lahir": "Bandung",
            "tanggal_lahir": "1980-03-10",
            "alamat": "Jl. Sudirman No. 456, Bandung",
            "no_hp": "081234567891",
            "pendidikan": "S1",
            "pekerjaan": "Wiraswasta",
            "penghasilan": "5000000.00",
            "penghasilan_formatted": "Rp 5.000.000",
            "url_photo": "https://example.com/photos/orangtua1.jpg",
            "url_cover": "https://example.com/covers/orangtua1.jpg",
            "is_active": true
        }
    }
}
```

#### 3. Role: `guru`, `wali-kelas`, atau `kepala-sekolah`
User dengan role guru, wali-kelas, atau kepala-sekolah akan mendapatkan informasi dari tabel `users` dan `guru`.

**Response Example:**
```json
{
    "success": true,
    "message": "User profile retrieved successfully",
    "data": {
        "id": 3,
        "name": "Siti Nurhaliza",
        "username": "siti_nurhaliza",
        "email": "guru@example.com",
        "email_verified_at": null,
        "role": "guru",
        "is_active": true,
        "created_at": "2024-10-20T10:30:00.000000Z",
        "updated_at": "2024-10-20T10:30:00.000000Z",
        "guru": {
            "id": 1,
            "nip": "198501012010012001",
            "nama": "Siti Nurhaliza",
            "jenis_kelamin": "P",
            "jenis_kelamin_text": "Perempuan",
            "tempat_lahir": "Surabaya",
            "tanggal_lahir": "1985-01-01",
            "alamat": "Jl. Pahlawan No. 789, Surabaya",
            "no_hp": "081234567892",
            "url_photo": "https://example.com/photos/guru1.jpg",
            "url_cover": "https://example.com/covers/guru1.jpg",
            "is_active": true
        }
    }
}
```

#### 4. Role: `tata-usaha`, `yayasan`, `admin`, atau `super-admin`
User dengan role administratif hanya mendapatkan informasi dari tabel `users` tanpa data tambahan.

**Response Example:**
```json
{
    "success": true,
    "message": "User profile retrieved successfully",
    "data": {
        "id": 4,
        "name": "Administrator",
        "username": "admin",
        "email": "admin@example.com",
        "email_verified_at": null,
        "role": "admin",
        "is_active": true,
        "created_at": "2024-10-20T10:30:00.000000Z",
        "updated_at": "2024-10-20T10:30:00.000000Z"
    }
}
```

### Error Responses

#### 401 Unauthorized
Ketika tidak ada token atau token tidak valid.

```json
{
    "message": "Unauthenticated."
}
```

#### 500 Internal Server Error
Ketika terjadi kesalahan di server.

```json
{
    "success": false,
    "message": "An error occurred while retrieving profile",
    "error": "Error details..."
}
```

## Related Endpoints

### Update Profile: `PUT /api/v1/auth/profile`
Untuk update informasi dasar user (name, email, password).

**Request Body:**
```json
{
    "name": "New Name",
    "email": "newemail@example.com",
    "current_password": "oldpassword123",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Note:** 
- Semua field bersifat optional
- `current_password` wajib jika ingin mengubah `password`
- Update data role-specific (siswa, guru, orang_tua) dilakukan melalui endpoint terpisah

### Logout: `POST /api/v1/auth/logout`
Untuk menghapus token saat ini dan logout.

## Implementation Details

### Controller: `AuthController.php`
```php
public function profile(Request $request)
{
    $user = $request->user();

    // Eager load role-specific relations
    switch ($user->role) {
        case 'siswa':
            $user->load('siswa.kelas');
            break;
        case 'orang-tua':
            $user->load('orangTua');
            break;
        case 'guru':
        case 'wali-kelas':
        case 'kepala-sekolah':
            $user->load('guru');
            break;
    }

    return response()->json([
        'success' => true,
        'message' => 'User profile retrieved successfully',
        'data' => new UserProfileResource($user)
    ], 200);
}
```

### Resource: `UserProfileResource.php`
Resource ini menggunakan conditional loading untuk menampilkan data yang berbeda berdasarkan role user.

### Model Relations

**User Model:**
- `hasOne(Siswa::class)` - Relasi ke tabel siswa
- `hasOne(Guru::class)` - Relasi ke tabel guru  
- `hasOne(OrangTua::class)` - Relasi ke tabel orang_tua

**Siswa Model:**
- `belongsTo(User::class)` - Relasi ke tabel users
- `belongsTo(Kelas::class)` - Relasi ke tabel kelas

**Guru Model:**
- `belongsTo(User::class)` - Relasi ke tabel users

**OrangTua Model:**
- `belongsTo(User::class)` - Relasi ke tabel users

## Testing

Jalankan test untuk memastikan endpoint berfungsi dengan benar:

```bash
php test_auth_profile.php
```

Test akan melakukan:
1. Login untuk setiap role
2. Request profile untuk setiap role
3. Validasi response structure
4. Validasi data yang dikembalikan sesuai role

## Best Practices Applied

1. **Eager Loading**: Menggunakan `load()` untuk menghindari N+1 query problem
2. **Resource Classes**: Menggunakan API Resource untuk transformasi data yang konsisten
3. **Conditional Loading**: Data tambahan hanya dimuat jika dibutuhkan berdasarkan role
4. **Type Safety**: Menggunakan strict type checking
5. **Error Handling**: Proper error handling dan response messages
6. **Authentication**: Middleware Sanctum untuk keamanan
7. **Documentation**: Comprehensive documentation dengan examples
8. **Testing**: Automated testing untuk semua scenarios
