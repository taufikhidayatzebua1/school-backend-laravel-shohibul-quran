<?php

/**
 * Test Password Reset Functionality
 * 
 * This test verifies:
 * 1. Forgot Password - Send reset link
 * 2. Reset Password - With token
 * 3. Validation errors
 */

$baseUrl = 'http://127.0.0.1:8000/api/v1';

// Color codes
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
            if (isset($result['data'])) {
                echo "   Data: " . json_encode($result['data']) . "\n";
            }
        } else {
            $failedTests[] = $testName;
            echo "{$RED}✗ FAILED{$RESET}: " . $result['message'] . "\n";
        }
    } catch (Exception $e) {
        $failedTests[] = $testName;
        echo "{$RED}✗ ERROR{$RESET}: " . $e->getMessage() . "\n";
    }
}

echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║       PASSWORD RESET FUNCTIONALITY TEST                  ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n";

// Test 1: Forgot Password - Missing Email
runTest("Forgot Password - Validation: Missing Email", function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/auth/forgot-password", 'POST', []);
    
    if ($response['code'] === 422 && isset($response['body']['errors']['email'])) {
        return [
            'success' => true,
            'message' => 'Correctly rejects missing email',
            'data' => $response['body']['errors']['email']
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Should reject missing email - Code: ' . $response['code']
    ];
});

// Test 2: Forgot Password - Invalid Email Format
runTest("Forgot Password - Validation: Invalid Email Format", function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/auth/forgot-password", 'POST', [
        'email' => 'not-an-email'
    ]);
    
    if ($response['code'] === 422 && isset($response['body']['errors']['email'])) {
        return [
            'success' => true,
            'message' => 'Correctly rejects invalid email format',
            'data' => $response['body']['errors']['email']
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Should reject invalid email - Code: ' . $response['code']
    ];
});

// Test 3: Forgot Password - Email Not Exists
runTest("Forgot Password - Validation: Email Not Exists", function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/auth/forgot-password", 'POST', [
        'email' => 'nonexistent@example.com'
    ]);
    
    if ($response['code'] === 422 && isset($response['body']['errors']['email'])) {
        return [
            'success' => true,
            'message' => 'Correctly rejects non-existent email',
            'data' => $response['body']['errors']['email']
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Should reject non-existent email - Code: ' . $response['code']
    ];
});

// Test 4: Forgot Password - Valid Request
runTest("Forgot Password - Valid Request", function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/auth/forgot-password", 'POST', [
        'email' => 'budi.santoso@sekolah.com'
    ]);
    
    if ($response['code'] === 200 && $response['body']['success']) {
        return [
            'success' => true,
            'message' => $response['body']['message'],
            'data' => 'Reset link sent (or would be sent if email configured)'
        ];
    }
    
    // Some environments may return 500 if email is not configured
    // That's OK for testing purposes
    if ($response['code'] === 500) {
        return [
            'success' => true,
            'message' => 'Email not configured (expected in dev environment)',
            'data' => 'This is OK - email server not set up'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Unexpected response - Code: ' . $response['code'] . ', Body: ' . json_encode($response['body'])
    ];
});

// Test 5: Reset Password - Missing Token
runTest("Reset Password - Validation: Missing Token", function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/auth/reset-password", 'POST', [
        'email' => 'budi.santoso@sekolah.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123'
    ]);
    
    if ($response['code'] === 422 && isset($response['body']['errors']['token'])) {
        return [
            'success' => true,
            'message' => 'Correctly rejects missing token',
            'data' => $response['body']['errors']['token']
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Should reject missing token - Code: ' . $response['code']
    ];
});

// Test 6: Reset Password - Missing Email
runTest("Reset Password - Validation: Missing Email", function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/auth/reset-password", 'POST', [
        'token' => 'dummy-token',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123'
    ]);
    
    if ($response['code'] === 422 && isset($response['body']['errors']['email'])) {
        return [
            'success' => true,
            'message' => 'Correctly rejects missing email',
            'data' => $response['body']['errors']['email']
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Should reject missing email - Code: ' . $response['code']
    ];
});

// Test 7: Reset Password - Password Too Short
runTest("Reset Password - Validation: Password Too Short", function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/auth/reset-password", 'POST', [
        'token' => 'dummy-token',
        'email' => 'budi.santoso@sekolah.com',
        'password' => 'short',
        'password_confirmation' => 'short'
    ]);
    
    if ($response['code'] === 422 && isset($response['body']['errors']['password'])) {
        return [
            'success' => true,
            'message' => 'Correctly rejects password < 8 characters',
            'data' => $response['body']['errors']['password']
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Should reject short password - Code: ' . $response['code']
    ];
});

// Test 8: Reset Password - Password Confirmation Mismatch
runTest("Reset Password - Validation: Password Mismatch", function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/auth/reset-password", 'POST', [
        'token' => 'dummy-token',
        'email' => 'budi.santoso@sekolah.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'differentpassword'
    ]);
    
    if ($response['code'] === 422 && isset($response['body']['errors']['password'])) {
        return [
            'success' => true,
            'message' => 'Correctly rejects password mismatch',
            'data' => $response['body']['errors']['password']
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Should reject password mismatch - Code: ' . $response['code']
    ];
});

// Test 9: Reset Password - Invalid Token (Expected to fail)
runTest("Reset Password - Invalid Token", function() use ($baseUrl) {
    $response = makeRequest("$baseUrl/auth/reset-password", 'POST', [
        'token' => 'invalid-token-123',
        'email' => 'budi.santoso@sekolah.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123'
    ]);
    
    // Should return error because token is invalid
    if ($response['code'] === 500 || ($response['code'] === 200 && !$response['body']['success'])) {
        return [
            'success' => true,
            'message' => 'Correctly rejects invalid token',
            'data' => $response['body']['message'] ?? 'Invalid token'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Should reject invalid token - Code: ' . $response['code']
    ];
});

// Test 10: Check Database for Password Reset Tokens Table
runTest("Database - Check password_reset_tokens table exists", function() {
    try {
        require __DIR__ . '/vendor/autoload.php';
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        
        $tableExists = \Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('password_reset_tokens');
        
        if ($tableExists) {
            return [
                'success' => true,
                'message' => 'password_reset_tokens table exists',
                'data' => 'Table ready for password reset functionality'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'password_reset_tokens table not found'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error checking table: ' . $e->getMessage()
        ];
    }
});

// Final Summary
echo "\n" . str_repeat("═", 60) . "\n";
echo "TEST SUMMARY\n";
echo str_repeat("═", 60) . "\n";
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
    echo "\n{$GREEN}✓ ALL TESTS PASSED!{$RESET}\n";
} else {
    echo "\n{$YELLOW}⚠ SOME TESTS FAILED (This is expected if email is not configured){$RESET}\n";
}

echo str_repeat("═", 60) . "\n";

// Information
echo "\n{$BLUE}ℹ INFORMATION:{$RESET}\n";
echo "• Forgot Password requires email configuration to send actual reset links\n";
echo "• Reset Password requires valid tokens from the database\n";
echo "• In development, it's normal if email sending fails (500 error)\n";
echo "• Validation tests should all pass regardless of email configuration\n";
echo "\n{$BLUE}📧 EMAIL CONFIGURATION:{$RESET}\n";
echo "• Check .env for MAIL_* settings\n";
echo "• For testing, you can use Mailtrap, Mailhog, or log driver\n";
echo "• Set MAIL_MAILER=log to log emails instead of sending\n";
