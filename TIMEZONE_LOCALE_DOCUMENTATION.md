# 🌏 TIMEZONE & LOCALE CONFIGURATION

## Configuration Summary

Aplikasi ini telah dikonfigurasi untuk menggunakan **Zona Waktu Indonesia** dan **Bahasa Indonesia**.

---

## 📍 Timezone Configuration

### **Default Timezone: Asia/Jakarta (WIB - GMT+7)**

#### Files Updated:
1. **`config/app.php`**
```php
'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta'),
```

2. **`.env` & `.env.example`**
```env
APP_TIMEZONE=Asia/Jakarta
```

### Available Indonesian Timezones:
- **WIB (Waktu Indonesia Barat)**: `Asia/Jakarta` - GMT+7
  - Jawa, Sumatera
  
- **WITA (Waktu Indonesia Tengah)**: `Asia/Makassar` - GMT+8
  - Bali, Kalimantan Selatan & Timur, Sulawesi, Nusa Tenggara
  
- **WIT (Waktu Indonesia Timur)**: `Asia/Jayapura` - GMT+9
  - Papua, Maluku

### How to Change Timezone:
Update `.env` file:
```env
APP_TIMEZONE=Asia/Jakarta    # WIB (Default)
APP_TIMEZONE=Asia/Makassar   # WITA
APP_TIMEZONE=Asia/Jayapura   # WIT
```

---

## 🌍 Locale Configuration

### **Default Locale: Indonesian (id)**

#### Files Updated:
1. **`config/app.php`**
```php
'locale' => env('APP_LOCALE', 'id'),
'fallback_locale' => env('APP_FALLBACK_LOCALE', 'id'),
'faker_locale' => env('APP_FAKER_LOCALE', 'id_ID'),
```

2. **`.env` & `.env.example`**
```env
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID
```

### What This Affects:

#### 1. **Carbon Date Formatting**
```php
use Carbon\Carbon;

Carbon::setLocale('id');
echo Carbon::now()->translatedFormat('l, d F Y');
// Output: Jumat, 17 Oktober 2025

echo Carbon::now()->diffForHumans();
// Output: 2 hari yang lalu
```

#### 2. **Laravel Translations**
All validation messages already use Indonesian (defined in Form Requests).

#### 3. **Faker Data**
When using seeders, Faker will generate Indonesian names, addresses, etc.

```php
$faker = Faker\Factory::create('id_ID');
echo $faker->name;  // Generates Indonesian name
```

---

## 📅 Usage Examples

### 1. Getting Current Time (Indonesian)
```php
use Carbon\Carbon;

// Basic
$now = Carbon::now(); // Already in Asia/Jakarta timezone
echo $now->format('Y-m-d H:i:s'); // 2025-10-17 01:45:31

// Indonesian Format
Carbon::setLocale('id');
echo $now->translatedFormat('l, d F Y H:i:s');
// Output: Jumat, 17 Oktober 2025 01:45:31
```

### 2. Database Timestamps
All `created_at` and `updated_at` timestamps automatically use configured timezone:

```php
$hafalan = Hafalan::create([...]);

// Timestamps are automatically in Asia/Jakarta timezone
echo $hafalan->created_at->format('Y-m-d H:i:s T');
// Output: 2025-10-17 01:45:31 WIB

echo $hafalan->created_at->translatedFormat('l, d F Y - H:i:s');
// Output: Jumat, 17 Oktober 2025 - 01:45:31
```

### 3. API Response with Indonesian Time
```php
return response()->json([
    'success' => true,
    'data' => [
        'created_at' => Carbon::now()->translatedFormat('l, d F Y H:i:s'),
        'timezone' => 'WIB',
        'timestamp' => time()
    ]
]);
```

### 4. Date Comparisons
```php
$start = Carbon::parse('2025-10-01 00:00:00'); // Parsed in Asia/Jakarta
$end = Carbon::parse('2025-10-17 23:59:59');

if (Carbon::now()->between($start, $end)) {
    echo "Dalam periode yang ditentukan";
}
```

---

## 🔧 Verification

### Check Current Configuration:
```bash
php artisan tinker
```
```php
config('app.timezone')        // Asia/Jakarta
config('app.locale')          // id
date_default_timezone_get()   // Asia/Jakarta
Carbon::now()->timezoneName   // Asia/Jakarta
```

### Run Test Scripts:
```bash
# Test Timezone & Locale
php test_timezone_locale.php

# Test Database Timestamps
php test_db_timezone.php
```

---

## 📊 Timezone Comparison

| Location | Timezone | Offset | Example Time |
|----------|----------|--------|--------------|
| **Jakarta (WIB)** | Asia/Jakarta | GMT+7 | 01:45:31 |
| **Makassar (WITA)** | Asia/Makassar | GMT+8 | 02:45:31 |
| **Jayapura (WIT)** | Asia/Jayapura | GMT+9 | 03:45:31 |
| **UTC** | UTC | GMT+0 | 18:45:31 |

---

## ⚠️ Important Notes

1. **Database Storage**: 
   - Laravel stores timestamps in database as **raw values** (not timezone-aware in MySQL)
   - Timezone conversion happens at application level
   - When retrieving, Laravel automatically converts to configured timezone

2. **API Clients**: 
   - If you have international clients, consider sending timestamps in **ISO 8601 format** with timezone
   - Example: `2025-10-17T01:45:31+07:00`

3. **Frontend Display**:
   - JavaScript Date objects may use browser's timezone
   - Always specify timezone when parsing dates in frontend
   - Consider using libraries like Moment.js or Day.js with timezone plugins

4. **Testing**:
   - When testing, ensure test database also respects timezone
   - Use Carbon::setTestNow() for time-dependent tests

---

## 🎯 Best Practices

1. **Always use Carbon** for date/time operations:
   ```php
   // ✅ Good
   $now = Carbon::now();
   
   // ❌ Avoid
   $now = date('Y-m-d H:i:s');
   ```

2. **Explicit Timezone in API Responses**:
   ```php
   return [
       'timestamp' => Carbon::now()->toIso8601String(), // Includes timezone
       'timezone' => 'WIB',
       'offset' => '+07:00'
   ];
   ```

3. **Date Input Validation**:
   ```php
   // In Form Request
   'tanggal' => 'required|date|after:today'
   ```

4. **Store UTC, Display Local** (Optional for international apps):
   ```php
   // Store in UTC
   $user->login_at = Carbon::now()->utc();
   
   // Display in configured timezone (automatically)
   echo $user->login_at->format('Y-m-d H:i:s'); // Converted to Asia/Jakarta
   ```

---

## 🚀 Production Checklist

- [x] Timezone set to `Asia/Jakarta` (or appropriate Indonesian timezone)
- [x] Locale set to `id` (Indonesian)
- [x] Faker locale set to `id_ID`
- [x] `.env` updated with timezone settings
- [x] Config cached after changes (`php artisan config:cache`)
- [x] Database migrations tested with Indonesian timezone
- [x] API responses include timezone information
- [x] Frontend handles timezone correctly

---

## 📝 Configuration Files Modified

1. ✅ `config/app.php` - Timezone & Locale defaults
2. ✅ `.env` - Environment-specific settings
3. ✅ `.env.example` - Template for new environments

---

## 🔄 Updating Configuration

After changing timezone or locale settings:

```bash
# Clear and recache configuration
php artisan config:clear
php artisan config:cache

# Restart application (if using queue workers or horizon)
php artisan queue:restart
```

---

**Last Updated**: October 17, 2025  
**Timezone**: Asia/Jakarta (WIB)  
**Locale**: Indonesian (id)
