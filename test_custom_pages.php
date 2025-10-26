<?php

/**
 * Test Script untuk Custom Page Builder API
 * 
 * Script ini untuk testing manual API Custom Page Builder
 * Pastikan sudah login dan mendapatkan token
 */

// Configuration
$baseUrl = 'http://localhost:8000/api/v1';
$adminEmail = 'admin@example.com';
$adminPassword = 'password';
$siswaEmail = 'siswa@example.com';
$siswaPassword = 'password';

// Helper function untuk melakukan HTTP request
function makeRequest($method, $url, $data = null, $token = null) {
    $ch = curl_init();
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];
    
    if ($token) {
        $headers[] = "Authorization: Bearer $token";
    }
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

// Helper function untuk print hasil
function printResult($title, $response) {
    echo "\n";
    echo "=" . str_repeat("=", 80) . "\n";
    echo "TEST: $title\n";
    echo "=" . str_repeat("=", 80) . "\n";
    echo "HTTP Code: " . $response['code'] . "\n";
    echo "Response:\n";
    echo json_encode($response['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n";
}

echo "🚀 Starting Custom Page Builder API Tests\n";
echo "=========================================\n";

// Test 1: Login sebagai Admin
echo "\n📝 Test 1: Login sebagai Admin\n";
$loginResponse = makeRequest('POST', "$baseUrl/auth/login", [
    'email' => $adminEmail,
    'password' => $adminPassword
]);
printResult("Login sebagai Admin", $loginResponse);

if ($loginResponse['code'] !== 200) {
    die("❌ Login failed! Please check your credentials.\n");
}

$adminToken = $loginResponse['body']['data']['token'];
echo "✅ Admin token obtained: " . substr($adminToken, 0, 20) . "...\n";

// Test 2: Create Custom Page untuk Siswa
echo "\n📝 Test 2: Create Custom Page untuk Siswa\n";
$createResponse1 = makeRequest('POST', "$baseUrl/custom-pages", [
    'title' => 'Panduan untuk Siswa',
    'html_content' => '<h1>Selamat Datang Siswa</h1><p>Ini adalah panduan lengkap untuk siswa...</p>',
    'role' => ['siswa', 'orang-tua']
], $adminToken);
printResult("Create Custom Page untuk Siswa", $createResponse1);
$page1Id = $createResponse1['body']['data']['id'] ?? null;

// Test 3: Create Custom Page untuk Guru
echo "\n📝 Test 3: Create Custom Page untuk Guru\n";
$createResponse2 = makeRequest('POST', "$baseUrl/custom-pages", [
    'title' => 'Panduan Input Nilai',
    'html_content' => '<h1>Cara Input Nilai</h1><ol><li>Login ke sistem</li><li>Masuk ke menu nilai</li><li>Input nilai siswa</li></ol>',
    'role' => ['guru', 'wali-kelas', 'kepala-sekolah']
], $adminToken);
printResult("Create Custom Page untuk Guru", $createResponse2);
$page2Id = $createResponse2['body']['data']['id'] ?? null;

// Test 4: Create Custom Page untuk Admin Only
echo "\n📝 Test 4: Create Custom Page untuk Admin Only\n";
$createResponse3 = makeRequest('POST', "$baseUrl/custom-pages", [
    'title' => 'Dashboard Admin',
    'html_content' => '<h1>Dashboard Khusus Admin</h1><p>Konten rahasia untuk admin...</p>',
    'role' => ['admin', 'super-admin']
], $adminToken);
printResult("Create Custom Page untuk Admin Only", $createResponse3);
$page3Id = $createResponse3['body']['data']['id'] ?? null;

// Test 5: Get All Custom Pages sebagai Admin
echo "\n📝 Test 5: Get All Custom Pages sebagai Admin\n";
$getAllAdminResponse = makeRequest('GET', "$baseUrl/custom-pages", null, $adminToken);
printResult("Get All Custom Pages sebagai Admin", $getAllAdminResponse);

// Test 6: Get Single Custom Page
if ($page1Id) {
    echo "\n📝 Test 6: Get Single Custom Page (ID: $page1Id)\n";
    $getSingleResponse = makeRequest('GET', "$baseUrl/custom-pages/$page1Id", null, $adminToken);
    printResult("Get Single Custom Page", $getSingleResponse);
}

// Test 7: Update Custom Page
if ($page1Id) {
    echo "\n📝 Test 7: Update Custom Page (ID: $page1Id)\n";
    $updateResponse = makeRequest('PUT', "$baseUrl/custom-pages/$page1Id", [
        'title' => 'Panduan untuk Siswa (UPDATED)',
        'html_content' => '<h1>Selamat Datang Siswa - Updated</h1><p>Ini adalah panduan yang sudah diupdate...</p>'
    ], $adminToken);
    printResult("Update Custom Page", $updateResponse);
}

// Test 8: Login sebagai Siswa
echo "\n📝 Test 8: Login sebagai Siswa\n";
$siswaLoginResponse = makeRequest('POST', "$baseUrl/auth/login", [
    'email' => $siswaEmail,
    'password' => $siswaPassword
]);
printResult("Login sebagai Siswa", $siswaLoginResponse);

if ($siswaLoginResponse['code'] === 200) {
    $siswaToken = $siswaLoginResponse['body']['data']['token'];
    echo "✅ Siswa token obtained: " . substr($siswaToken, 0, 20) . "...\n";
    
    // Test 9: Get All Custom Pages sebagai Siswa (hanya yang role-nya siswa)
    echo "\n📝 Test 9: Get All Custom Pages sebagai Siswa\n";
    $getAllSiswaResponse = makeRequest('GET', "$baseUrl/custom-pages", null, $siswaToken);
    printResult("Get All Custom Pages sebagai Siswa", $getAllSiswaResponse);
    
    // Test 10: Siswa mencoba akses halaman guru (should fail)
    if ($page2Id) {
        echo "\n📝 Test 10: Siswa mencoba akses halaman Guru (ID: $page2Id) - Should Fail\n";
        $unauthorizedResponse = makeRequest('GET', "$baseUrl/custom-pages/$page2Id", null, $siswaToken);
        printResult("Siswa akses halaman Guru (Unauthorized)", $unauthorizedResponse);
    }
    
    // Test 11: Siswa mencoba create halaman (should fail)
    echo "\n📝 Test 11: Siswa mencoba Create Custom Page - Should Fail\n";
    $unauthorizedCreateResponse = makeRequest('POST', "$baseUrl/custom-pages", [
        'title' => 'Halaman dari Siswa',
        'html_content' => '<h1>Test</h1>',
        'role' => ['siswa']
    ], $siswaToken);
    printResult("Siswa Create Custom Page (Unauthorized)", $unauthorizedCreateResponse);
} else {
    echo "⚠️ Siswa login failed, skipping siswa tests\n";
}

// Test 12: Search Custom Pages
echo "\n📝 Test 12: Search Custom Pages (search='panduan')\n";
$searchResponse = makeRequest('GET', "$baseUrl/custom-pages?search=panduan", null, $adminToken);
printResult("Search Custom Pages", $searchResponse);

// Test 13: Pagination
echo "\n📝 Test 13: Get Custom Pages with Pagination (per_page=2)\n";
$paginationResponse = makeRequest('GET', "$baseUrl/custom-pages?per_page=2", null, $adminToken);
printResult("Get Custom Pages with Pagination", $paginationResponse);

// Test 14: Validation Error - Empty title
echo "\n📝 Test 14: Create Custom Page dengan Title kosong (Validation Error)\n";
$validationErrorResponse = makeRequest('POST', "$baseUrl/custom-pages", [
    'title' => '',
    'html_content' => '<h1>Test</h1>',
    'role' => ['siswa']
], $adminToken);
printResult("Validation Error - Empty title", $validationErrorResponse);

// Test 15: Validation Error - Invalid role
echo "\n📝 Test 15: Create Custom Page dengan Invalid Role (Validation Error)\n";
$invalidRoleResponse = makeRequest('POST', "$baseUrl/custom-pages", [
    'title' => 'Test Page',
    'html_content' => '<h1>Test</h1>',
    'role' => ['invalid-role']
], $adminToken);
printResult("Validation Error - Invalid role", $invalidRoleResponse);

// Test 16: Validation Error - Empty role array
echo "\n📝 Test 16: Create Custom Page dengan Empty Role Array (Validation Error)\n";
$emptyRoleResponse = makeRequest('POST', "$baseUrl/custom-pages", [
    'title' => 'Test Page',
    'html_content' => '<h1>Test</h1>',
    'role' => []
], $adminToken);
printResult("Validation Error - Empty role array", $emptyRoleResponse);

// Test 17: Delete Custom Page
if ($page3Id) {
    echo "\n📝 Test 17: Delete Custom Page (ID: $page3Id)\n";
    $deleteResponse = makeRequest('DELETE', "$baseUrl/custom-pages/$page3Id", null, $adminToken);
    printResult("Delete Custom Page", $deleteResponse);
}

// Test 18: Get deleted page (should return 404)
if ($page3Id) {
    echo "\n📝 Test 18: Get Deleted Custom Page (ID: $page3Id) - Should Return 404\n";
    $notFoundResponse = makeRequest('GET', "$baseUrl/custom-pages/$page3Id", null, $adminToken);
    printResult("Get Deleted Custom Page (404)", $notFoundResponse);
}

echo "\n";
echo "=" . str_repeat("=", 80) . "\n";
echo "✅ All Tests Completed!\n";
echo "=" . str_repeat("=", 80) . "\n";
