<?php

/**
 * Test Hafalan API with Corrected Field Names
 * 
 * This test verifies that the Hafalan API works correctly after fixing
 * the field naming inconsistency (surah_id, ayat_dari, ayat_sampai, keterangan)
 */

$baseUrl = 'http://127.0.0.1:8000/api/v1';
$publicUrl = 'http://127.0.0.1:8000/api/v1/public';

// Color codes for output
$GREEN = "\033[32m";
$RED = "\033[31m";
$YELLOW = "\033[33m";
$BLUE = "\033[34m";
$RESET = "\033[0m";

$totalTests = 0;
$passedTests = 0;
$failedTests = [];

function makeRequest($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init($url);
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-Request-ID: test-' . uniqid()
    ];
    
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

function runTest($testName, $callback) {
    global $totalTests, $passedTests, $failedTests, $GREEN, $RED, $RESET;
    
    $totalTests++;
    echo "\n" . ($totalTests) . ". Testing: $testName\n";
    
    try {
        $result = $callback();
        if ($result['success']) {
            $passedTests++;
            echo "{$GREEN}✓ PASSED{$RESET}: " . $result['message'] . "\n";
        } else {
            $failedTests[] = $testName;
            echo "{$RED}✗ FAILED{$RESET}: " . $result['message'] . "\n";
        }
    } catch (Exception $e) {
        $failedTests[] = $testName;
        echo "{$RED}✗ ERROR{$RESET}: " . $e->getMessage() . "\n";
    }
}

echo "=== HAFALAN API FIELD NAME CONSISTENCY TEST ===\n";
echo "Testing with corrected field names: surah_id, ayat_dari, ayat_sampai, keterangan\n";

// Step 1: Login as Guru
runTest("Login as Guru", function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/auth/login", 'POST', [
        'email' => 'budi.santoso@sekolah.com',
        'password' => 'password123'
    ]);
    
    if ($response['code'] === 200 && isset($response['body']['data']['access_token'])) {
        global $guruToken;
        $guruToken = $response['body']['data']['access_token'];
        return [
            'success' => true,
            'message' => 'Login successful, token obtained'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Login failed - Code: ' . $response['code'] . ', Response: ' . json_encode($response['body'])
    ];
});

// Step 2: Get Hafalan List (Public API)
runTest("Get Hafalan List (Public API)", function() use ($publicUrl) {
    $response = makeRequest("$publicUrl/hafalan");
    
    if ($response['code'] === 200 && isset($response['body']['data'])) {
        $hafalan = $response['body']['data'][0] ?? null;
        
        // Check for correct field names (Public API excludes keterangan)
        $requiredFields = ['surah_id', 'ayat_dari', 'ayat_sampai'];
        $oldFields = ['surat', 'ayat', 'catatan'];
        
        foreach ($requiredFields as $field) {
            if (!isset($hafalan[$field])) {
                return [
                    'success' => false,
                    'message' => "Missing required field: $field"
                ];
            }
        }
        
        foreach ($oldFields as $field) {
            if (isset($hafalan[$field])) {
                return [
                    'success' => false,
                    'message' => "Old field name found: $field (should not exist)"
                ];
            }
        }
        
        global $hafalanId;
        $hafalanId = $hafalan['id'];
        
        return [
            'success' => true,
            'message' => 'All correct field names present, old field names not found'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Failed to get hafalan list - Code: ' . $response['code']
    ];
});

// Step 3: Get Single Hafalan (Public API)
runTest("Get Single Hafalan (Public API)", function() use ($publicUrl) {
    global $hafalanId;
    $response = makeRequest("$publicUrl/hafalan/$hafalanId");
    
    if ($response['code'] === 200 && isset($response['body']['data'])) {
        $hafalan = $response['body']['data'];
        
        // Verify correct field names (Public API excludes keterangan)
        $requiredFields = ['surah_id', 'ayat_dari', 'ayat_sampai'];
        foreach ($requiredFields as $field) {
            if (!isset($hafalan[$field])) {
                return [
                    'success' => false,
                    'message' => "Missing required field: $field"
                ];
            }
        }
        
        return [
            'success' => true,
            'message' => "Single hafalan has correct field names (surah_id={$hafalan['surah_id']}, ayat_dari={$hafalan['ayat_dari']}, ayat_sampai={$hafalan['ayat_sampai']})"
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Failed to get single hafalan - Code: ' . $response['code']
    ];
});

// Step 4: Create Hafalan with New Field Names
runTest("Create Hafalan with Correct Field Names", function() use ($baseUrl) {
    global $guruToken;
    
    $response = makeRequest("$baseUrl/hafalan", 'POST', [
        'siswa_id' => 1,
        'guru_id' => 1,  // Guru is required
        'surah_id' => 112,  // Al-Ikhlas
        'ayat_dari' => 1,
        'ayat_sampai' => 4,
        'status' => 'lancar',
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Test hafalan dengan field names yang benar'
    ], $guruToken);
    
    if ($response['code'] === 201 && isset($response['body']['data'])) {
        global $newHafalanId;
        $newHafalanId = $response['body']['data']['id'];
        
        $hafalan = $response['body']['data'];
        
        // Verify response has correct field names
        if ($hafalan['surah_id'] === 112 && 
            $hafalan['ayat_dari'] === 1 && 
            $hafalan['ayat_sampai'] === 4 &&
            $hafalan['keterangan'] === 'Test hafalan dengan field names yang benar') {
            return [
                'success' => true,
                'message' => 'Hafalan created successfully with correct field names'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Hafalan created but field values incorrect'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Failed to create hafalan - Code: ' . $response['code'] . ', Response: ' . json_encode($response['body'])
    ];
});

// Step 5: Update Hafalan with Correct Field Names
runTest("Update Hafalan with Correct Field Names", function() use ($baseUrl) {
    global $guruToken, $newHafalanId;
    
    // Skip if hafalan wasn't created
    if (!isset($newHafalanId)) {
        return [
            'success' => false,
            'message' => 'Skipped - no hafalan to update'
        ];
    }
    
    $response = makeRequest("$baseUrl/hafalan/$newHafalanId", 'PUT', [
        'siswa_id' => 1,
        'guru_id' => 1,
        'surah_id' => 113,  // Al-Falaq
        'ayat_dari' => 1,
        'ayat_sampai' => 5,
        'status' => 'perlu_bimbingan',
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Updated dengan field names yang benar'
    ], $guruToken);
    
    if ($response['code'] === 200 && isset($response['body']['data'])) {
        $hafalan = $response['body']['data'];
        
        if ($hafalan['surah_id'] === 113 && 
            $hafalan['ayat_dari'] === 1 && 
            $hafalan['ayat_sampai'] === 5) {
            return [
                'success' => true,
                'message' => 'Hafalan updated successfully with correct field names'
            ];
        }
    }
    
    return [
        'success' => false,
        'message' => 'Failed to update hafalan - Code: ' . $response['code'] . ', Response: ' . json_encode($response['body'])
    ];
});

// Step 6: Validate surah_id Range (max 114)
runTest("Validation: surah_id max 114", function() use ($baseUrl) {
    global $guruToken;
    
    $response = makeRequest("$baseUrl/hafalan", 'POST', [
        'siswa_id' => 1,
        'guru_id' => 1,
        'surah_id' => 115,  // Invalid: > 114
        'ayat_dari' => 1,
        'ayat_sampai' => 5,
        'status' => 'lancar',
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Test validation'
    ], $guruToken);
    
    if ($response['code'] === 422 && isset($response['body']['errors']['surah_id'])) {
        return [
            'success' => true,
            'message' => 'Validation correctly rejected surah_id > 114'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Validation failed to reject invalid surah_id'
    ];
});

// Step 7: Validate ayat_sampai >= ayat_dari
runTest("Validation: ayat_sampai >= ayat_dari", function() use ($baseUrl) {
    global $guruToken;
    
    $response = makeRequest("$baseUrl/hafalan", 'POST', [
        'siswa_id' => 1,
        'guru_id' => 1,
        'surah_id' => 112,
        'ayat_dari' => 4,
        'ayat_sampai' => 2,  // Invalid: < ayat_dari
        'status' => 'lancar',
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Test validation'
    ], $guruToken);
    
    if ($response['code'] === 422 && isset($response['body']['errors']['ayat_sampai'])) {
        return [
            'success' => true,
            'message' => 'Validation correctly rejected ayat_sampai < ayat_dari'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Validation failed to reject invalid ayat range'
    ];
});

// Step 8: Verify Protected API Returns More Fields
runTest("Protected API Returns All Fields", function() use ($baseUrl) {
    global $guruToken;
    
    $response = makeRequest("$baseUrl/hafalan", 'GET', null, $guruToken);
    
    if ($response['code'] === 200 && isset($response['body']['data'][0])) {
        $hafalan = $response['body']['data'][0];
        
        // Protected API should have guru and siswa relationships
        if (isset($hafalan['guru']) && isset($hafalan['siswa'])) {
            return [
                'success' => true,
                'message' => 'Protected API includes relationships (guru, siswa)'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Protected API missing relationships'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Failed to get protected hafalan list'
    ];
});

// Step 9: Delete Test Hafalan
runTest("Delete Test Hafalan", function() use ($baseUrl) {
    global $guruToken, $newHafalanId;
    
    // Skip if hafalan wasn't created
    if (!isset($newHafalanId)) {
        return [
            'success' => false,
            'message' => 'Skipped - no hafalan to delete'
        ];
    }
    
    $response = makeRequest("$baseUrl/hafalan/$newHafalanId", 'DELETE', null, $guruToken);
    
    if ($response['code'] === 200) {
        return [
            'success' => true,
            'message' => 'Test hafalan deleted successfully'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Failed to delete test hafalan - Code: ' . $response['code']
    ];
});

// Final Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "TEST SUMMARY\n";
echo str_repeat("=", 60) . "\n";
echo "Total Tests: $totalTests\n";
echo "{$GREEN}Passed: $passedTests{$RESET}\n";
echo "{$RED}Failed: " . count($failedTests) . "{$RESET}\n";

if (count($failedTests) > 0) {
    echo "\nFailed Tests:\n";
    foreach ($failedTests as $test) {
        echo "  - $test\n";
    }
}

$successRate = ($totalTests > 0) ? round(($passedTests / $totalTests) * 100, 2) : 0;
echo "\nSuccess Rate: {$successRate}%\n";

if ($passedTests === $totalTests) {
    echo "\n{$GREEN}✓ ALL TESTS PASSED! Field name consistency verified.{$RESET}\n";
} else {
    echo "\n{$RED}✗ SOME TESTS FAILED. Please review the errors above.{$RESET}\n";
}

echo str_repeat("=", 60) . "\n";
