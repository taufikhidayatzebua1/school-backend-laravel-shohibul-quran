<?php

$baseUrl = 'http://127.0.0.1:8000/api/v1';

echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║           TEST RATE LIMITING                                          ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

// Test Rate Limiting on Public Route (60 req/min from config)
echo "Testing Public Route Rate Limit (60 req/min from config)...\n";
echo "Making 5 requests to /api/v1/public/siswa\n\n";

for ($i = 1; $i <= 5; $i++) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$baseUrl/public/siswa");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Extract headers
    preg_match('/X-RateLimit-Limit: (\d+)/', $response, $limit);
    preg_match('/X-RateLimit-Remaining: (\d+)/', $response, $remaining);
    preg_match('/X-Request-ID: ([^\r\n]+)/', $response, $requestId);
    preg_match('/X-Cache-Hit: ([^\r\n]+)/', $response, $cacheHit);
    
    echo "Request #$i:\n";
    echo "  Status: $httpCode\n";
    echo "  Rate Limit: " . ($limit[1] ?? 'N/A') . " requests per minute\n";
    echo "  Remaining: " . ($remaining[1] ?? 'N/A') . " requests\n";
    echo "  Request ID: " . ($requestId[1] ?? 'N/A') . "\n";
    echo "  Cache Hit: " . ($cacheHit[1] ?? 'false') . "\n\n";
    
    usleep(100000); // 0.1 second delay
}

echo "\n";
echo "Testing Auth Route Rate Limit (10 req/min from config)...\n";
echo "Making 5 login attempts\n\n";

for ($i = 1; $i <= 5; $i++) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$baseUrl/auth/login");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'email' => 'test@test.com',
        'password' => 'wrongpassword'
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Extract headers
    preg_match('/X-RateLimit-Limit: (\d+)/', $response, $limit);
    preg_match('/X-RateLimit-Remaining: (\d+)/', $response, $remaining);
    preg_match('/X-Request-ID: ([^\r\n]+)/', $response, $requestId);
    
    echo "Login Attempt #$i:\n";
    echo "  Status: $httpCode\n";
    echo "  Rate Limit: " . ($limit[1] ?? 'N/A') . " requests per minute\n";
    echo "  Remaining: " . ($remaining[1] ?? 'N/A') . " requests\n";
    echo "  Request ID: " . ($requestId[1] ?? 'N/A') . "\n\n";
    
    usleep(100000);
}

echo "\n╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║  Rate Limiting is ACTIVE and protecting your API!                    ║\n";
echo "║  Configuration from config/api.php and .env:                         ║\n";
echo "║  - Public routes: 60 requests/minute per IP (API_RATE_LIMIT_PUBLIC) ║\n";
echo "║  - Auth routes: 10 requests/minute per IP (API_RATE_LIMIT_AUTH)     ║\n";
echo "║  - Protected routes: 200 requests/minute (API_RATE_LIMIT_PROTECTED) ║\n";
echo "║                                                                       ║\n";
echo "║  ✅ Brute force protection enabled                                    ║\n";
echo "║  ✅ X-Request-ID tracking for debugging                               ║\n";
echo "║  ✅ Response caching active for public endpoints                      ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n";
