<?php

/**
 * Test script untuk validasi registrasi user tanpa role
 * 
 * Testing:
 * 1. Register tanpa role → should default to 'siswa'
 * 2. Register dengan role → should use provided role
 * 3. Register dengan role invalid → should fail validation
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Hash;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║        TEST REGISTRASI USER (ROLE OPTIONAL)             ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

// Test 1: Register tanpa role
echo "📝 Test 1: Register user TANPA role field\n";
echo str_repeat("-", 60) . "\n";

$request1 = new \Illuminate\Http\Request();
$request1->merge([
    'name' => 'Test User No Role',
    'email' => 'test_norole_' . time() . '@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
]);

$validator1 = \Illuminate\Support\Facades\Validator::make($request1->all(), [
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users',
    'password' => 'required|string|min:8|confirmed',
    'role' => 'nullable|in:siswa,orang-tua,guru,wali-kelas,kepala-sekolah,tata-usaha,yayasan,admin,super-admin',
]);

if ($validator1->fails()) {
    echo "❌ Validation FAILED:\n";
    print_r($validator1->errors()->toArray());
} else {
    echo "✅ Validation PASSED\n";
    
    // Simulate user creation
    $userData1 = [
        'name' => $request1->name,
        'email' => $request1->email,
        'password' => Hash::make($request1->password),
    ];
    
    if ($request1->filled('role')) {
        $userData1['role'] = $request1->role;
    }
    
    $user1 = \App\Models\User::create($userData1);
    
    echo "   User ID: {$user1->id}\n";
    echo "   Name: {$user1->name}\n";
    echo "   Email: {$user1->email}\n";
    echo "   Role: {$user1->role} " . ($user1->role === 'siswa' ? '✅ (default)' : '❌') . "\n";
    echo "   Is Active: " . ($user1->is_active ? 'true ✅ (default)' : 'false') . "\n";
}

echo "\n";

// Test 2: Register dengan role
echo "📝 Test 2: Register user DENGAN role 'guru'\n";
echo str_repeat("-", 60) . "\n";

$request2 = new \Illuminate\Http\Request();
$request2->merge([
    'name' => 'Test User Guru',
    'email' => 'test_guru_' . time() . '@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 'guru',
]);

$validator2 = \Illuminate\Support\Facades\Validator::make($request2->all(), [
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users',
    'password' => 'required|string|min:8|confirmed',
    'role' => 'nullable|in:siswa,orang-tua,guru,wali-kelas,kepala-sekolah,tata-usaha,yayasan,admin,super-admin',
]);

if ($validator2->fails()) {
    echo "❌ Validation FAILED:\n";
    print_r($validator2->errors()->toArray());
} else {
    echo "✅ Validation PASSED\n";
    
    $userData2 = [
        'name' => $request2->name,
        'email' => $request2->email,
        'password' => Hash::make($request2->password),
    ];
    
    if ($request2->filled('role')) {
        $userData2['role'] = $request2->role;
    }
    
    $user2 = \App\Models\User::create($userData2);
    
    echo "   User ID: {$user2->id}\n";
    echo "   Name: {$user2->name}\n";
    echo "   Email: {$user2->email}\n";
    echo "   Role: {$user2->role} " . ($user2->role === 'guru' ? '✅ (as requested)' : '❌') . "\n";
    echo "   Is Active: " . ($user2->is_active ? 'true ✅' : 'false') . "\n";
}

echo "\n";

// Test 3: Register dengan role invalid
echo "📝 Test 3: Register user dengan role INVALID\n";
echo str_repeat("-", 60) . "\n";

$request3 = new \Illuminate\Http\Request();
$request3->merge([
    'name' => 'Test User Invalid',
    'email' => 'test_invalid_' . time() . '@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 'super-duper-admin', // Invalid role
]);

$validator3 = \Illuminate\Support\Facades\Validator::make($request3->all(), [
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users',
    'password' => 'required|string|min:8|confirmed',
    'role' => 'nullable|in:siswa,orang-tua,guru,wali-kelas,kepala-sekolah,tata-usaha,yayasan,admin,super-admin',
], [
    'role.in' => 'Role yang dipilih tidak valid. Pilihan: siswa, orang-tua, guru, wali-kelas, kepala-sekolah, tata-usaha, yayasan, admin, super-admin.',
]);

if ($validator3->fails()) {
    echo "✅ Validation CORRECTLY FAILED\n";
    echo "   Errors:\n";
    foreach ($validator3->errors()->all() as $error) {
        echo "   - {$error}\n";
    }
} else {
    echo "❌ Validation SHOULD HAVE FAILED but passed!\n";
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║                    TEST SELESAI                          ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n";
