# 🔥 QUICK START - Password Reset Testing

## ✅ READY TO TEST NOW!

### 📱 Halaman Testing Sudah Terbuka!

Halaman **test-forgot-password.html** sudah terbuka di Simple Browser VS Code.

---

## 🚀 LANGKAH TESTING (Sangat Mudah!)

### Step 1: Kirim Email Reset Password

**Di halaman yang sudah terbuka:**

1. ✅ Email sudah terisi otomatis: `taufikhizet1350@gmail.com`
2. 🖱️ **Klik tombol besar:** "📨 Kirim Link Reset Password"
3. ⏳ Tunggu beberapa detik...
4. ✅ Akan muncul notifikasi hijau: "Link reset password berhasil dikirim!"

**Jika ada error:**
- Pastikan server Laravel berjalan
- Cek koneksi internet untuk SMTP

---

### Step 2: Cek Email di Gmail

1. 🌐 Buka browser baru: **https://gmail.com**

2. 🔐 Login dengan:
   - **Email:** taufikhizet1350@gmail.com
   - **Password:** [password Gmail Anda]

3. 📧 Cari email dengan subject:
   ```
   Reset Password - Laravel
   ```
   atau
   ```
   Reset Password Notification
   ```

4. 📂 Cek folder:
   - ✉️ **Inbox** (cek di sini dulu)
   - 🗑️ **Spam** (jika tidak ada di Inbox)

5. 📄 Email akan berisi:
   - Greeting: "Hello Taufik Hizet!"
   - Tombol/Link: "Reset Password"
   - Token untuk reset password
   - Peringatan: Link expires in 60 minutes

---

### Step 3: Reset Password

**OPTION A: Klik Link di Email (Paling Mudah)**

1. 🖱️ Klik tombol **"Reset Password"** di email
2. ✨ Otomatis buka halaman reset password
3. ✅ Token dan email sudah terisi otomatis
4. 🔑 Masukkan password baru (minimal 8 karakter)
5. 🔑 Konfirmasi password (ketik ulang password yang sama)
6. 🖱️ Klik: **"🔐 Reset Password"**

**OPTION B: Copy-Paste Token Manual**

1. 📋 Copy token dari email (long string of characters)
2. 🌐 Buka di browser baru:
   ```
   http://127.0.0.1:8000/test-reset-password.html
   ```
3. 📝 Paste token di field "Token Reset Password"
4. 📧 Masukkan email: `taufikhizet1350@gmail.com`
5. 🔑 Masukkan password baru (minimal 8 karakter)
6. 🔑 Konfirmasi password
7. 🖱️ Klik: **"🔐 Reset Password"**

---

### Step 4: Verifikasi Success

**Tanda-tanda berhasil:**

✅ Muncul notifikasi hijau:
```
✅ Password berhasil direset!
Anda sekarang bisa login dengan password baru Anda.
Email: taufikhizet1350@gmail.com
Password Baru: [password yang Anda masukkan]
```

✅ Form reset password otomatis kosong/clear

✅ Bisa login dengan password baru

---

## 🎯 Test Login dengan Password Baru

### Via Web (Jika ada halaman login):
```
http://127.0.0.1:8000/login
Email: taufikhizet1350@gmail.com
Password: [password baru Anda]
```

### Via API (PowerShell):
```powershell
$body = @{
    email = "taufikhizet1350@gmail.com"
    password = "password_baru_anda"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/v1/auth/login" `
  -Method POST `
  -ContentType "application/json" `
  -Body $body
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": { ... },
    "token": "your-auth-token"
  }
}
```

---

## 📝 INFORMASI PENTING

### Akun Testing:
- **Name:** Taufik Hizet
- **Email:** taufikhizet1350@gmail.com
- **Role:** Kepala Sekolah
- **Password Lama:** password123

### Email Configuration:
- **SMTP Host:** mail.hizet.my.id
- **Port:** 465 (SSL)
- **From:** sq1@hizet.my.id
- **To:** taufikhizet1350@gmail.com

### Token Info:
- **Expires:** 60 minutes after creation
- **One-time use:** Each token can only be used once
- **Case sensitive:** Copy exactly as shown

---

## 🔧 Troubleshooting

### ❌ "Cannot connect to server"
**Solution:**
```bash
# Check if server running
netstat -ano | findstr :8000

# If not, start server
php artisan serve
```

### ❌ Email tidak diterima
**Check:**
1. ✅ Folder Spam di Gmail
2. ✅ Email configuration di .env
3. ✅ Internet connection
4. ✅ SMTP server: mail.hizet.my.id:465

**Re-send email:**
```
Klik lagi tombol "Kirim Link Reset Password"
```

### ❌ "Token is invalid"
**Causes:**
- Token sudah expired (>60 menit)
- Token salah copy (ada spasi atau karakter kurang)
- Token sudah pernah dipakai

**Solution:**
```
Request token baru dengan klik "Kirim Link Reset Password" lagi
```

### ❌ "Password too short"
**Solution:**
```
Password minimal 8 karakter
Contoh: newpassword123
```

### ❌ "Password confirmation doesn't match"
**Solution:**
```
Pastikan password dan konfirmasi password SAMA PERSIS
```

---

## 🎬 VIDEO WALKTHROUGH (Step by Step)

```
1. [✓] Halaman test-forgot-password.html sudah terbuka
2. [→] Klik "Kirim Link Reset Password"
3. [✓] Lihat notifikasi sukses
4. [→] Buka Gmail di browser lain
5. [→] Login dengan taufikhizet1350@gmail.com
6. [→] Cari email "Reset Password"
7. [→] Klik tombol "Reset Password" di email
8. [→] Masukkan password baru (2x)
9. [→] Klik "Reset Password"
10. [✓] Lihat notifikasi sukses
11. [→] Test login dengan password baru
12. [✓] DONE! 🎉
```

---

## 🔗 Quick Links

| Link | URL |
|------|-----|
| **Forgot Password** | http://127.0.0.1:8000/test-forgot-password.html |
| **Reset Password** | http://127.0.0.1:8000/test-reset-password.html |
| **Gmail** | https://gmail.com |
| **API Docs** | PASSWORD_RESET_TESTING_GUIDE.md |

---

## ✨ NEXT STEPS

Sekarang Anda bisa:

1. ✅ **Klik tombol** di halaman yang sudah terbuka
2. 📧 **Cek Gmail** untuk mendapatkan token
3. 🔐 **Reset password** dengan token dari email
4. 🎉 **Test login** dengan password baru!

---

**Status:** 🟢 READY TO TEST  
**Server:** 🟢 RUNNING (http://127.0.0.1:8000)  
**Email:** 🟢 CONFIGURED  
**Pages:** 🟢 AVAILABLE  

**LET'S GO! 🚀**

---

*Last Updated: October 18, 2025*  
*Testing by: Taufik Hizet*
