<?php

/**
 * Test Auth Profile Endpoint
 * 
 * Endpoint: GET /api/v1/auth/profile
 * 
 * Test cases:
 * 1. Get profile as siswa (should include siswa data and kelas)
 * 2. Get profile as orang-tua (should include orang_tua data)
 * 3. Get profile as guru (should include guru data)
 * 4. Get profile as wali-kelas (should include guru data)
 * 5. Get profile as kepala-sekolah (should include guru data)
 * 6. Get profile as admin (should only include user data)
 */

require_once __DIR__ . '/vendor/autoload.php';

// Configuration
$baseUrl = 'http://sq-backend.test/api/v1';
$apiVersion = 'v1';

// ANSI color codes
define('COLOR_GREEN', "\033[32m");
define('COLOR_RED', "\033[31m");
define('COLOR_YELLOW', "\033[33m");
define('COLOR_BLUE', "\033[34m");
define('COLOR_RESET', "\033[0m");

/**
 * Make HTTP request
 */
function makeRequest($method, $endpoint, $data = null, $token = null)
{
    $ch = curl_init();
    $url = $GLOBALS['baseUrl'] . $endpoint;

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

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

/**
 * Login and get token
 */
function login($email, $password)
{
    $response = makeRequest('POST', '/auth/login', [
        'email' => $email,
        'password' => $password
    ]);

    if ($response['code'] === 200 && isset($response['body']['data']['access_token'])) {
        return $response['body']['data']['access_token'];
    }

    return null;
}

/**
 * Print test result
 */
function printResult($testName, $passed, $message = '')
{
    $status = $passed ? COLOR_GREEN . '✓ PASS' : COLOR_RED . '✗ FAIL';
    echo "{$status}" . COLOR_RESET . " - {$testName}";
    if ($message) {
        echo " ({$message})";
    }
    echo "\n";
    return $passed;
}

/**
 * Print section header
 */
function printSection($title)
{
    echo "\n" . COLOR_BLUE . "═══════════════════════════════════════════════════════" . COLOR_RESET . "\n";
    echo COLOR_BLUE . "  {$title}" . COLOR_RESET . "\n";
    echo COLOR_BLUE . "═══════════════════════════════════════════════════════" . COLOR_RESET . "\n\n";
}

// Test credentials (adjust based on your seeder data)
$testUsers = [
    'siswa' => ['email' => 'siswa@example.com', 'password' => 'password123'],
    'orang-tua' => ['email' => 'orangtua@example.com', 'password' => 'password123'],
    'guru' => ['email' => 'guru@example.com', 'password' => 'password123'],
    'wali-kelas' => ['email' => 'walikelas@example.com', 'password' => 'password123'],
    'kepala-sekolah' => ['email' => 'kepalasekolah@example.com', 'password' => 'password123'],
    'admin' => ['email' => 'admin@example.com', 'password' => 'password123'],
];

$totalTests = 0;
$passedTests = 0;

printSection('AUTH PROFILE ENDPOINT TESTS');

// Test 1: Get profile as Siswa
printSection('Test 1: Get Profile as Siswa');
$token = login($testUsers['siswa']['email'], $testUsers['siswa']['password']);
if ($token) {
    $response = makeRequest('GET', '/auth/profile', null, $token);
    $totalTests++;
    
    $passed = $response['code'] === 200 &&
              isset($response['body']['data']['role']) &&
              $response['body']['data']['role'] === 'siswa' &&
              isset($response['body']['data']['siswa']);
    
    if (printResult('Siswa profile retrieved', $passed)) {
        $passedTests++;
        
        // Check siswa data
        $siswaData = $response['body']['data']['siswa'];
        if (isset($siswaData['nis'], $siswaData['nama'], $siswaData['kelas'])) {
            echo COLOR_YELLOW . "  → Siswa data: NIS={$siswaData['nis']}, Nama={$siswaData['nama']}" . COLOR_RESET . "\n";
            if ($siswaData['kelas']) {
                echo COLOR_YELLOW . "  → Kelas: {$siswaData['kelas']['nama']}" . COLOR_RESET . "\n";
            }
        }
    }
} else {
    echo COLOR_RED . "Failed to login as siswa" . COLOR_RESET . "\n";
}

// Test 2: Get profile as Orang Tua
printSection('Test 2: Get Profile as Orang Tua');
$token = login($testUsers['orang-tua']['email'], $testUsers['orang-tua']['password']);
if ($token) {
    $response = makeRequest('GET', '/auth/profile', null, $token);
    $totalTests++;
    
    $passed = $response['code'] === 200 &&
              isset($response['body']['data']['role']) &&
              $response['body']['data']['role'] === 'orang-tua' &&
              isset($response['body']['data']['orang_tua']);
    
    if (printResult('Orang Tua profile retrieved', $passed)) {
        $passedTests++;
        
        // Check orang tua data
        $orangTuaData = $response['body']['data']['orang_tua'];
        if (isset($orangTuaData['nama'])) {
            echo COLOR_YELLOW . "  → Orang Tua: {$orangTuaData['nama']}" . COLOR_RESET . "\n";
            if (isset($orangTuaData['pekerjaan'])) {
                echo COLOR_YELLOW . "  → Pekerjaan: {$orangTuaData['pekerjaan']}" . COLOR_RESET . "\n";
            }
        }
    }
} else {
    echo COLOR_RED . "Failed to login as orang-tua" . COLOR_RESET . "\n";
}

// Test 3: Get profile as Guru
printSection('Test 3: Get Profile as Guru');
$token = login($testUsers['guru']['email'], $testUsers['guru']['password']);
if ($token) {
    $response = makeRequest('GET', '/auth/profile', null, $token);
    $totalTests++;
    
    $passed = $response['code'] === 200 &&
              isset($response['body']['data']['role']) &&
              $response['body']['data']['role'] === 'guru' &&
              isset($response['body']['data']['guru']);
    
    if (printResult('Guru profile retrieved', $passed)) {
        $passedTests++;
        
        // Check guru data
        $guruData = $response['body']['data']['guru'];
        if (isset($guruData['nip'], $guruData['nama'])) {
            echo COLOR_YELLOW . "  → Guru: NIP={$guruData['nip']}, Nama={$guruData['nama']}" . COLOR_RESET . "\n";
        }
    }
} else {
    echo COLOR_RED . "Failed to login as guru" . COLOR_RESET . "\n";
}

// Test 4: Get profile as Wali Kelas
printSection('Test 4: Get Profile as Wali Kelas');
$token = login($testUsers['wali-kelas']['email'], $testUsers['wali-kelas']['password']);
if ($token) {
    $response = makeRequest('GET', '/auth/profile', null, $token);
    $totalTests++;
    
    $passed = $response['code'] === 200 &&
              isset($response['body']['data']['role']) &&
              $response['body']['data']['role'] === 'wali-kelas' &&
              isset($response['body']['data']['guru']);
    
    if (printResult('Wali Kelas profile retrieved', $passed)) {
        $passedTests++;
        
        // Check guru data
        $guruData = $response['body']['data']['guru'];
        if (isset($guruData['nip'], $guruData['nama'])) {
            echo COLOR_YELLOW . "  → Wali Kelas: NIP={$guruData['nip']}, Nama={$guruData['nama']}" . COLOR_RESET . "\n";
        }
    }
} else {
    echo COLOR_RED . "Failed to login as wali-kelas" . COLOR_RESET . "\n";
}

// Test 5: Get profile as Kepala Sekolah
printSection('Test 5: Get Profile as Kepala Sekolah');
$token = login($testUsers['kepala-sekolah']['email'], $testUsers['kepala-sekolah']['password']);
if ($token) {
    $response = makeRequest('GET', '/auth/profile', null, $token);
    $totalTests++;
    
    $passed = $response['code'] === 200 &&
              isset($response['body']['data']['role']) &&
              $response['body']['data']['role'] === 'kepala-sekolah' &&
              isset($response['body']['data']['guru']);
    
    if (printResult('Kepala Sekolah profile retrieved', $passed)) {
        $passedTests++;
        
        // Check guru data
        $guruData = $response['body']['data']['guru'];
        if (isset($guruData['nip'], $guruData['nama'])) {
            echo COLOR_YELLOW . "  → Kepala Sekolah: NIP={$guruData['nip']}, Nama={$guruData['nama']}" . COLOR_RESET . "\n";
        }
    }
} else {
    echo COLOR_RED . "Failed to login as kepala-sekolah" . COLOR_RESET . "\n";
}

// Test 6: Get profile as Admin
printSection('Test 6: Get Profile as Admin');
$token = login($testUsers['admin']['email'], $testUsers['admin']['password']);
if ($token) {
    $response = makeRequest('GET', '/auth/profile', null, $token);
    $totalTests++;
    
    $passed = $response['code'] === 200 &&
              isset($response['body']['data']['role']) &&
              $response['body']['data']['role'] === 'admin' &&
              !isset($response['body']['data']['siswa']) &&
              !isset($response['body']['data']['guru']) &&
              !isset($response['body']['data']['orang_tua']);
    
    if (printResult('Admin profile retrieved (no additional data)', $passed)) {
        $passedTests++;
        echo COLOR_YELLOW . "  → Admin: {$response['body']['data']['name']}" . COLOR_RESET . "\n";
    }
} else {
    echo COLOR_RED . "Failed to login as admin" . COLOR_RESET . "\n";
}

// Test 7: Unauthorized access
printSection('Test 7: Unauthorized Access');
$response = makeRequest('GET', '/auth/profile', null, null);
$totalTests++;
$passed = $response['code'] === 401;
if (printResult('Unauthorized access rejected', $passed)) {
    $passedTests++;
}

// Summary
printSection('TEST SUMMARY');
$percentage = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 2) : 0;
echo "Total Tests: {$totalTests}\n";
echo COLOR_GREEN . "Passed: {$passedTests}" . COLOR_RESET . "\n";
echo COLOR_RED . "Failed: " . ($totalTests - $passedTests) . COLOR_RESET . "\n";
echo "Success Rate: {$percentage}%\n\n";

if ($passedTests === $totalTests) {
    echo COLOR_GREEN . "🎉 All tests passed!" . COLOR_RESET . "\n";
    exit(0);
} else {
    echo COLOR_RED . "❌ Some tests failed" . COLOR_RESET . "\n";
    exit(1);
}
