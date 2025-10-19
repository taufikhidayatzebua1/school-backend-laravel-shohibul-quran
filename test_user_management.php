<?php

/**
 * Test User Management dengan Role-Based Access Control
 * 
 * File ini untuk testing manual endpoint user management
 * Jalankan dengan cara load di browser atau gunakan Postman
 * 
 * BASE URL: http://localhost/api/v1
 */

// Configuration
$baseUrl = 'http://localhost/api/v1';

// Test Users Credentials
$testUsers = [
    'super-admin' => [
        'email' => 'superadmin@example.com',
        'password' => 'password123',
    ],
    'admin' => [
        'email' => 'admin@example.com',
        'password' => 'password123',
    ],
    'tata-usaha' => [
        'email' => 'tatausaha@example.com',
        'password' => 'password123',
    ],
];

/**
 * Helper function untuk HTTP request
 */
function makeRequest($method, $url, $data = null, $token = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = "Authorization: Bearer $token";
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'body' => json_decode($response, true),
    ];
}

/**
 * Login dan dapatkan token
 */
function login($baseUrl, $email, $password) {
    $response = makeRequest('POST', "$baseUrl/auth/login", [
        'email' => $email,
        'password' => $password,
    ]);
    
    if ($response['status'] === 200 && isset($response['body']['data']['access_token'])) {
        return $response['body']['data']['access_token'];
    }
    
    return null;
}

/**
 * Test Cases
 */
function runTests($baseUrl, $testUsers) {
    echo "\n";
    echo "╔═══════════════════════════════════════════════════════════════════╗\n";
    echo "║           TEST USER MANAGEMENT - ROLE-BASED ACCESS               ║\n";
    echo "╚═══════════════════════════════════════════════════════════════════╝\n\n";
    
    // Test 1: Tata-usaha create siswa (SHOULD SUCCESS)
    echo "TEST 1: Tata-usaha create siswa (Expected: ✅ SUCCESS)\n";
    echo "─────────────────────────────────────────────────────────────────\n";
    $token = login($baseUrl, $testUsers['tata-usaha']['email'], $testUsers['tata-usaha']['password']);
    if ($token) {
        $response = makeRequest('POST', "$baseUrl/auth/register", [
            'name' => 'Test Siswa',
            'email' => 'testsiswa' . time() . '@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'siswa',
        ], $token);
        
        echo "Status: {$response['status']}\n";
        echo "Message: " . ($response['body']['message'] ?? 'N/A') . "\n";
        if ($response['status'] === 201) {
            echo "Result: ✅ SUCCESS - Tata-usaha berhasil create siswa\n";
        } else {
            echo "Result: ❌ FAILED\n";
            print_r($response['body']);
        }
    } else {
        echo "❌ Login failed\n";
    }
    echo "\n";
    
    // Test 2: Tata-usaha create admin (SHOULD FAIL)
    echo "TEST 2: Tata-usaha create admin (Expected: ❌ FAIL)\n";
    echo "─────────────────────────────────────────────────────────────────\n";
    if ($token) {
        $response = makeRequest('POST', "$baseUrl/auth/register", [
            'name' => 'Test Admin',
            'email' => 'testadmin' . time() . '@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ], $token);
        
        echo "Status: {$response['status']}\n";
        if ($response['status'] === 422) {
            echo "Result: ✅ SUCCESS - Validasi bekerja, tata-usaha tidak bisa create admin\n";
            echo "Error: " . ($response['body']['errors']['role'][0] ?? 'N/A') . "\n";
        } else {
            echo "Result: ❌ FAILED - Seharusnya ditolak\n";
            print_r($response['body']);
        }
    }
    echo "\n";
    
    // Test 3: Admin create guru (SHOULD SUCCESS)
    echo "TEST 3: Admin create guru (Expected: ✅ SUCCESS)\n";
    echo "─────────────────────────────────────────────────────────────────\n";
    $token = login($baseUrl, $testUsers['admin']['email'], $testUsers['admin']['password']);
    if ($token) {
        $response = makeRequest('POST', "$baseUrl/auth/register", [
            'name' => 'Test Guru',
            'email' => 'testguru' . time() . '@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'guru',
        ], $token);
        
        echo "Status: {$response['status']}\n";
        echo "Message: " . ($response['body']['message'] ?? 'N/A') . "\n";
        if ($response['status'] === 201) {
            echo "Result: ✅ SUCCESS - Admin berhasil create guru\n";
        } else {
            echo "Result: ❌ FAILED\n";
            print_r($response['body']);
        }
    } else {
        echo "❌ Login failed\n";
    }
    echo "\n";
    
    // Test 4: Admin create super-admin (SHOULD FAIL)
    echo "TEST 4: Admin create super-admin (Expected: ❌ FAIL)\n";
    echo "─────────────────────────────────────────────────────────────────\n";
    if ($token) {
        $response = makeRequest('POST', "$baseUrl/auth/register", [
            'name' => 'Test Super Admin',
            'email' => 'testsuperadmin' . time() . '@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'super-admin',
        ], $token);
        
        echo "Status: {$response['status']}\n";
        if ($response['status'] === 422) {
            echo "Result: ✅ SUCCESS - Validasi bekerja, admin tidak bisa create super-admin\n";
            echo "Error: " . ($response['body']['errors']['role'][0] ?? 'N/A') . "\n";
        } else {
            echo "Result: ❌ FAILED - Seharusnya ditolak\n";
            print_r($response['body']);
        }
    }
    echo "\n";
    
    // Test 5: Super-admin create admin (SHOULD SUCCESS)
    echo "TEST 5: Super-admin create admin (Expected: ✅ SUCCESS)\n";
    echo "─────────────────────────────────────────────────────────────────\n";
    $token = login($baseUrl, $testUsers['super-admin']['email'], $testUsers['super-admin']['password']);
    if ($token) {
        $response = makeRequest('POST', "$baseUrl/auth/register", [
            'name' => 'Test Admin by Super',
            'email' => 'testadminbysuper' . time() . '@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ], $token);
        
        echo "Status: {$response['status']}\n";
        echo "Message: " . ($response['body']['message'] ?? 'N/A') . "\n";
        if ($response['status'] === 201) {
            echo "Result: ✅ SUCCESS - Super-admin berhasil create admin\n";
        } else {
            echo "Result: ❌ FAILED\n";
            print_r($response['body']);
        }
    } else {
        echo "❌ Login failed\n";
    }
    echo "\n";
    
    // Test 6: Get available roles
    echo "TEST 6: Get available roles untuk masing-masing role\n";
    echo "─────────────────────────────────────────────────────────────────\n";
    foreach ($testUsers as $roleName => $credentials) {
        $token = login($baseUrl, $credentials['email'], $credentials['password']);
        if ($token) {
            $response = makeRequest('GET', "$baseUrl/users/available-roles", null, $token);
            echo "$roleName dapat create role: ";
            if ($response['status'] === 200 && isset($response['body']['data']['roles'])) {
                echo implode(', ', array_keys($response['body']['data']['roles'])) . "\n";
            } else {
                echo "Error\n";
            }
        }
    }
    echo "\n";
    
    // Test 7: List users
    echo "TEST 7: List users (tata-usaha)\n";
    echo "─────────────────────────────────────────────────────────────────\n";
    $token = login($baseUrl, $testUsers['tata-usaha']['email'], $testUsers['tata-usaha']['password']);
    if ($token) {
        $response = makeRequest('GET', "$baseUrl/users?per_page=5", null, $token);
        echo "Status: {$response['status']}\n";
        if ($response['status'] === 200) {
            echo "Result: ✅ SUCCESS - Retrieved " . ($response['body']['meta']['total'] ?? 0) . " users\n";
        } else {
            echo "Result: ❌ FAILED\n";
        }
    }
    echo "\n";
    
    echo "╔═══════════════════════════════════════════════════════════════════╗\n";
    echo "║                      TEST COMPLETED                               ║\n";
    echo "╚═══════════════════════════════════════════════════════════════════╝\n\n";
}

// Run tests jika dijalankan langsung
if (php_sapi_name() === 'cli') {
    runTests($baseUrl, $testUsers);
} else {
    echo "<pre>";
    runTests($baseUrl, $testUsers);
    echo "</pre>";
}
