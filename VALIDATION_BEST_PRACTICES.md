# Laravel Validation Best Practices

## 🎯 Kesimpulan: BEST PRACTICE

**Validasi harus dilakukan di Form Request Classes, BUKAN di Controller!**

## ❌ MASALAH YANG DITEMUKAN

### File: `HafalanController.php`

**INKONSISTENSI:**
- Method `store()` ✅ Menggunakan `StoreHafalanRequest` (BENAR)
- Method `update()` ❌ Menggunakan `$request->validate()` di controller (SALAH)

**Duplikasi Validasi:**
- Validasi `ayat_sampai >= ayat_dari` dilakukan 2x:
  1. Di `StoreHafalanRequest` dengan rule `gte:ayat_dari` ✅
  2. Di `HafalanController::store()` dengan manual check ❌ (duplikat)
  3. Di `HafalanController::update()` dengan manual check ❌ (duplikat)

---

## 📋 BEST PRACTICE VALIDATION

### ✅ 1. GUNAKAN FORM REQUEST CLASSES

**Lokasi:** `app/Http/Requests/`

**Keuntungan:**
- ✅ Single Responsibility Principle
- ✅ Reusable validation logic
- ✅ Automatic error responses (422)
- ✅ Centralized validation rules
- ✅ Custom error messages
- ✅ Authorization logic
- ✅ Easier to test
- ✅ Cleaner controllers

**Struktur:**
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHafalanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // atau logic authorization
    }

    public function rules(): array
    {
        return [
            'field' => 'required|rule1|rule2',
            // Semua validasi di sini
        ];
    }

    public function messages(): array
    {
        return [
            'field.required' => 'Custom message',
        ];
    }
}
```

**Penggunaan di Controller:**
```php
public function store(StoreHafalanRequest $request)
{
    // Validasi sudah otomatis dilakukan
    $validated = $request->validated();
    
    // Langsung proses data
    $model = Model::create($validated);
    
    return response()->json([...]);
}
```

### ❌ 2. JANGAN VALIDASI DI CONTROLLER

**SALAH:**
```php
public function update(Request $request, $id)
{
    $validated = $request->validate([
        'field' => 'required|rule',
        // Validasi di controller = BAD PRACTICE
    ]);
}
```

**BENAR:**
```php
public function update(UpdateHafalanRequest $request, $id)
{
    // Validasi otomatis dari Request class
    $validated = $request->validated();
}
```

### ❌ 3. JANGAN DUPLIKASI VALIDASI

**SALAH:**
```php
class StoreHafalanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ayat_sampai' => 'gte:ayat_dari', // Sudah ada validasi
        ];
    }
}

class HafalanController
{
    public function store(StoreHafalanRequest $request)
    {
        $validated = $request->validated();
        
        // ❌ DUPLIKASI - sudah divalidasi di Request
        if ($validated['ayat_sampai'] < $validated['ayat_dari']) {
            return response()->json([...], 422);
        }
    }
}
```

**BENAR:**
```php
class StoreHafalanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ayat_sampai' => 'gte:ayat_dari', // Cukup di sini saja
        ];
    }
}

class HafalanController
{
    public function store(StoreHafalanRequest $request)
    {
        // Langsung create, validasi sudah dilakukan
        $hafalan = Hafalan::create($request->validated());
    }
}
```

---

## 🔧 ATURAN VALIDASI SPESIFIK

### Complex Validation

Jika ada validasi kompleks yang tidak bisa ditangani oleh built-in rules:

**Option 1: Custom Validation Rule (RECOMMENDED)**
```php
// app/Rules/AyatRangeRule.php
class AyatRangeRule implements Rule
{
    public function passes($attribute, $value)
    {
        $ayatDari = request('ayat_dari');
        return $value >= $ayatDari;
    }
}

// Di Request
'ayat_sampai' => ['required', new AyatRangeRule]
```

**Option 2: WithValidator Hook**
```php
class StoreHafalanRequest extends FormRequest
{
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->ayat_sampai < $this->ayat_dari) {
                $validator->errors()->add('ayat_sampai', 
                    'Ayat sampai harus >= ayat dari');
            }
        });
    }
}
```

---

## 📊 AUDIT HASIL

### HafalanController
- ❌ `update()` - Validasi di controller, harus pakai `UpdateHafalanRequest`
- ❌ `store()` - Duplikasi validasi `ayat_sampai >= ayat_dari`
- ❌ `update()` - Duplikasi validasi `ayat_sampai >= ayat_dari`

### KelasController
- ✅ Tidak ada validasi inline di controller
- ✅ Menggunakan Request classes (jika ada CRUD)

### SiswaController  
- ✅ Tidak ada validasi inline di controller
- ✅ Menggunakan Request classes (jika ada CRUD)

---

## 🎯 ACTION ITEMS

### 1. Fix HafalanController
- [ ] Remove manual validation dari `store()` method
- [ ] Change `update()` to use `UpdateHafalanRequest`
- [ ] Remove duplicate validation logic

### 2. Verify All Controllers
- [x] HafalanController - NEEDS FIX
- [x] KelasController - OK
- [x] SiswaController - OK

### 3. Verify All Request Classes
- [x] StoreHafalanRequest - OK (has `gte:ayat_dari`)
- [x] UpdateHafalanRequest - OK (has `gte:ayat_dari`)

---

## 📝 COMPARISON: BEFORE vs AFTER

### BEFORE (Current - INCONSISTENT)
```php
// HafalanController.php
public function store(StoreHafalanRequest $request) 
{
    $validated = $request->validated(); // ✅ Good
    
    // ❌ BAD - Duplicate validation (already in Request)
    if ($validated['ayat_sampai'] < $validated['ayat_dari']) {
        return response()->json([...], 422);
    }
    
    $hafalan = Hafalan::create($validated);
}

public function update(Request $request, $id) 
{
    // ❌ BAD - Should use UpdateHafalanRequest
    $validated = $request->validate([
        'siswa_id' => 'sometimes|required|exists:siswa,id',
        // ... many rules
    ]);
    
    // ❌ BAD - Duplicate validation
    if (isset($validated['ayat_dari']) && ...) {
        return response()->json([...], 422);
    }
}
```

### AFTER (Recommended - CONSISTENT)
```php
// HafalanController.php
public function store(StoreHafalanRequest $request) 
{
    // Validation automatic, no duplicate checks needed
    $hafalan = Hafalan::create($request->validated());
    $hafalan->load(['siswa', 'guru']);
    
    return response()->json([...], 201);
}

public function update(UpdateHafalanRequest $request, $id) 
{
    $hafalan = Hafalan::findOrFail($id);
    
    // Validation automatic, no duplicate checks needed
    $hafalan->update($request->validated());
    $hafalan->load(['siswa', 'guru']);
    
    return response()->json([...]);
}
```

---

## 🎓 LARAVEL BEST PRACTICES SUMMARY

1. **Form Requests** untuk semua validasi
2. **NO validation** di controller
3. **NO duplicate** validation logic
4. **Custom Rules** untuk validasi kompleks
5. **Type hinting** Request classes di controller methods
6. **Consistent** approach di semua controllers

---

## 📚 RESOURCES

- [Laravel Form Request Validation](https://laravel.com/docs/validation#form-request-validation)
- [Creating Custom Validation Rules](https://laravel.com/docs/validation#custom-validation-rules)
- [Available Validation Rules](https://laravel.com/docs/validation#available-validation-rules)

---

## ✅ CONCLUSION

**Current Status:** ❌ INCONSISTENT  
**Target Status:** ✅ CONSISTENT  
**Action Required:** Fix `HafalanController` to follow best practices

**Principle:** 
> "Validation logic belongs in Form Request classes, NOT in controllers. Controllers should only orchestrate the flow, not validate data."
