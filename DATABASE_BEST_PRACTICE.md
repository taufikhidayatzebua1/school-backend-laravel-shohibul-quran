# Database Connection Best Practices

## 📋 Overview

Aplikasi ini dikonfigurasi dengan **graceful degradation** untuk database connection. Artinya:
- ✅ Aplikasi tetap berjalan meskipun database down
- ✅ Otomatis fallback ke file-based session/cache
- ✅ Siap production tanpa perlu ubah konfigurasi
- ✅ Monitoring built-in untuk tracking database status

## 🔧 Konfigurasi

### 1. AppServiceProvider - Auto Fallback Logic

File: `app/Providers/AppServiceProvider.php`

```php
protected function handleDatabaseConnection(): void
{
    try {
        DB::connection()->getPdo();
        // Database OK - gunakan database session
        config(['session.driver' => 'database']);
    } catch (\Exception $e) {
        // Database down - fallback ke file
        config(['session.driver' => 'file']);
        config(['cache.default' => 'file']);
        config(['queue.default' => 'sync']);
    }
}
```

### 2. Database Config - Connection Timeout

File: `config/database.php`

```php
'options' => [
    PDO::ATTR_TIMEOUT => env('DB_TIMEOUT', 5), // 5 detik timeout
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]
```

### 3. Environment Variables

File: `.env`

```env
DB_TIMEOUT=5                 # Timeout 5 detik
SESSION_DRIVER=database       # Dengan auto-fallback
```

## 🏥 Health Check Endpoint

Akses: `GET /health`

Response ketika database connected:
```json
{
    "app": "ok",
    "database": "connected",
    "session_driver": "database",
    "timestamp": "2025-10-16T10:00:00.000000Z"
}
```

Response ketika database disconnected:
```json
{
    "app": "ok",
    "database": "disconnected",
    "session_driver": "file",
    "message": "Application running with fallback drivers",
    "timestamp": "2025-10-16T10:00:00.000000Z"
}
```

## 🚀 Keuntungan Implementasi Ini

### Development
- ✅ Tidak error saat database down
- ✅ Bisa test aplikasi tanpa database
- ✅ Faster startup time saat database lambat
- ✅ Header `X-Database-Status` untuk debugging

### Production
- ✅ High availability - aplikasi tetap serve request
- ✅ Graceful degradation saat database maintenance
- ✅ Automatic recovery saat database kembali online
- ✅ Logging untuk monitoring (hanya di production)
- ✅ No configuration change needed

## 📊 Skenario Testing

### Test 1: Database Online
```bash
# Homepage
curl http://localhost:8000/

# Health check
curl http://localhost:8000/health
```

### Test 2: Database Offline
```bash
# Ubah DB_HOST ke invalid host di .env
DB_HOST=invalid_host

# Clear config
php artisan config:clear

# Test homepage - TETAP BERJALAN
curl http://localhost:8000/

# Health check - Show disconnected status
curl http://localhost:8000/health
```

### Test 3: Database Recovery
```bash
# Kembalikan DB_HOST ke host yang valid
DB_HOST=livezet.id

# Clear config
php artisan config:clear

# Test - OTOMATIS kembali ke database session
curl http://localhost:8000/health
```

## ⚙️ Fallback Behavior

| Service | Normal | Fallback (DB Down) |
|---------|--------|-------------------|
| Session | database | file |
| Cache   | database | file |
| Queue   | database | sync |

## 🔍 Monitoring di Production

### Log Format
```
[warning] Database connection failed, using fallback drivers
{
    "error": "SQLSTATE[HY000] [2002] Connection refused",
    "session_driver": "file",
    "cache_driver": "file"
}
```

### Rekomendasi Monitoring
1. Setup alert untuk log warning database connection
2. Monitor `/health` endpoint setiap 30 detik
3. Track response time untuk deteksi database slowness
4. Setup auto-restart jika database down > 5 menit

## 🎯 Production Checklist

- [x] Auto-fallback mechanism implemented
- [x] Connection timeout configured (5s)
- [x] Health check endpoint ready
- [x] Error logging for monitoring
- [x] No config change needed between env
- [x] Graceful degradation tested

## 📝 Notes

1. **Session Data**: Saat fallback ke file, session yang ada di database tidak accessible. Setelah database kembali, session baru akan pakai database lagi.

2. **Queue Jobs**: Saat fallback, queue jobs akan sync (langsung eksekusi). Job yang di database queue akan diproses saat database kembali online.

3. **Cache**: Cache di database akan tidak accessible saat down. Cache akan rebuilt saat database kembali.

4. **Migration**: Run migration memerlukan database connection. Fallback tidak berlaku untuk artisan commands.

## 🔄 Recovery Process

System akan otomatis cek database connection setiap request:
1. Request masuk → AppServiceProvider boot
2. Test database connection
3. Jika sukses → gunakan database drivers
4. Jika gagal → gunakan file drivers
5. Next request akan test lagi

**No manual intervention needed!**
