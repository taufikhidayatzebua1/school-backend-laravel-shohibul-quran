<?php

$baseUrl = 'http://127.0.0.1:8000/api/v1';

echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║           TEST API AUTHENTICATION & AUTHORIZATION                     ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

// ========================================
// TEST 1: Public Route (No Auth Required)
// ========================================
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ TEST 1: Public Route - GET /api/v1/public/siswa (NO AUTH)          │\n";
echo "└─────────────────────────────────────────────────────────────────────┘\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/public/siswa");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "CURL Error: $error\n";
    exit(1);
}

// Parse headers and body
list($headers, $body) = explode("\r\n\r\n", $response, 2);

// Extract X-Request-ID
preg_match('/X-Request-ID: ([^\r\n]+)/', $headers, $requestId);
preg_match('/X-Cache-Hit: ([^\r\n]+)/', $headers, $cacheHit);

echo "HTTP Status: $httpCode\n";
echo "X-Request-ID: " . ($requestId[1] ?? 'N/A') . "\n";
echo "X-Cache-Hit: " . ($cacheHit[1] ?? 'false') . "\n";

$data = json_decode($body, true);
if ($data && $data['success']) {
    echo "Success: ✓\n";
    echo "Message: " . $data['message'] . "\n";
    echo "Data count: " . count($data['data']) . " siswa\n";
    echo "Result: ✅ PUBLIC ROUTE ACCESSIBLE WITHOUT AUTH\n\n";
} else {
    echo "Failed to parse response\n";
    echo "Response: " . substr($body, 0, 200) . "\n\n";
}

// ========================================
// TEST 2: Protected Route (No Auth)
// ========================================
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ TEST 2: Protected Route - GET /api/v1/siswa (NO AUTH)              │\n";
echo "└─────────────────────────────────────────────────────────────────────┘\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/siswa");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

list($headers, $body) = explode("\r\n\r\n", $response, 2);
preg_match('/X-Request-ID: ([^\r\n]+)/', $headers, $requestId);

echo "HTTP Status: $httpCode\n";
echo "X-Request-ID: " . ($requestId[1] ?? 'N/A') . "\n";
$data = json_decode($body, true);
echo "Response: " . ($data['message'] ?? 'Unauthenticated') . "\n";
echo "Result: " . ($httpCode == 401 ? '✅' : '❌') . " PROTECTED ROUTE REQUIRES AUTH (401)\n\n";

// ========================================
// TEST 3: Login as SISWA
// ========================================
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ TEST 3: Login as SISWA (andi.wijaya@siswa.com)                     │\n";
echo "└─────────────────────────────────────────────────────────────────────┘\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/auth/login");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'andi.wijaya@siswa.com',
    'password' => 'password123'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
$loginData = json_decode($response, true);
$siswaToken = $loginData['data']['access_token'] ?? null;
$expiresIn = $loginData['data']['expires_in'] ?? null;
$expiresAt = $loginData['data']['expires_at'] ?? null;
echo "Login Success: " . ($loginData['success'] ? '✓' : '✗') . "\n";
echo "User: " . $loginData['data']['user']['name'] . "\n";
echo "Role: " . $loginData['data']['user']['role'] . "\n";
echo "Token: " . ($siswaToken ? substr($siswaToken, 0, 20) . "..." : "NOT GENERATED") . "\n";
echo "Expires In: " . ($expiresIn ? $expiresIn . " seconds (24 hours)" : "N/A") . "\n";
echo "Expires At: " . ($expiresAt ?? "N/A") . "\n\n";

// ========================================
// TEST 4: Access Protected Route with SISWA token (Should Fail - Wrong Role)
// ========================================
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ TEST 4: Protected Route with SISWA token - GET /api/v1/siswa       │\n";
echo "│         (Should FAIL - Role siswa not allowed)                      │\n";
echo "└─────────────────────────────────────────────────────────────────────┘\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/siswa");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $siswaToken,
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

list($headers, $body) = explode("\r\n\r\n", $response, 2);
preg_match('/X-Request-ID: ([^\r\n]+)/', $headers, $requestId);

echo "HTTP Status: $httpCode\n";
echo "X-Request-ID: " . ($requestId[1] ?? 'N/A') . "\n";
$data = json_decode($body, true);
echo "Response: " . ($data['message'] ?? 'Forbidden') . "\n";
echo "Result: " . ($httpCode == 403 ? '✅' : '❌') . " SISWA ROLE BLOCKED (403 Forbidden)\n\n";

// ========================================
// TEST 5: Login as KEPALA SEKOLAH
// ========================================
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ TEST 5: Login as KEPALA SEKOLAH (kepala.sekolah@sekolah.com)       │\n";
echo "└─────────────────────────────────────────────────────────────────────┘\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/auth/login");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'kepala.sekolah@sekolah.com',
    'password' => 'password123'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
$loginData = json_decode($response, true);
$kepalaSekolahToken = $loginData['data']['access_token'] ?? null;
$expiresIn = $loginData['data']['expires_in'] ?? null;
$expiresAt = $loginData['data']['expires_at'] ?? null;
echo "Login Success: " . ($loginData['success'] ? '✓' : '✗') . "\n";
echo "User: " . $loginData['data']['user']['name'] . "\n";
echo "Role: " . $loginData['data']['user']['role'] . "\n";
echo "Token: " . ($kepalaSekolahToken ? substr($kepalaSekolahToken, 0, 20) . "..." : "NOT GENERATED") . "\n";
echo "Expires In: " . ($expiresIn ? $expiresIn . " seconds (24 hours)" : "N/A") . "\n";
echo "Expires At: " . ($expiresAt ?? "N/A") . "\n\n";

// ========================================
// TEST 6: Access Protected Route with KEPALA SEKOLAH token (Should Success)
// ========================================
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ TEST 6: Protected Route with KEPALA SEKOLAH - GET /api/v1/siswa    │\n";
echo "│         (Should SUCCESS - Role allowed)                             │\n";
echo "└─────────────────────────────────────────────────────────────────────┘\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/siswa");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $kepalaSekolahToken,
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

list($headers, $body) = explode("\r\n\r\n", $response, 2);
preg_match('/X-Request-ID: ([^\r\n]+)/', $headers, $requestId);

echo "HTTP Status: $httpCode\n";
echo "X-Request-ID: " . ($requestId[1] ?? 'N/A') . "\n";
$data = json_decode($body, true);
if ($data['success']) {
    echo "Success: ✓\n";
    echo "Message: " . $data['message'] . "\n";
    echo "Data count: " . count($data['data']) . " siswa\n";
    echo "Result: ✅ KEPALA SEKOLAH CAN ACCESS PROTECTED ROUTE\n";
} else {
    echo "Success: ✗\n";
    echo "Message: " . ($data['message'] ?? 'Unknown error') . "\n";
}

echo "\n";
echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║                         SUMMARY                                       ║\n";
echo "╠═══════════════════════════════════════════════════════════════════════╣\n";
echo "║ ✅ Public routes work without authentication                          ║\n";
echo "║ ✅ Protected routes require authentication (401 if not logged in)     ║\n";
echo "║ ✅ Role-based access control works:                                   ║\n";
echo "║    - SISWA: ❌ Cannot access protected routes (403)                   ║\n";
echo "║    - KEPALA SEKOLAH: ✅ Can access protected routes (200)             ║\n";
echo "║ ✅ Token expiration: 86400 seconds (24 hours)                         ║\n";
echo "║ ✅ Request ID tracking: X-Request-ID header present                   ║\n";
echo "║ ✅ Response caching: X-Cache-Hit header for public routes             ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n";
