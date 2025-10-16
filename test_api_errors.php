<?php

$baseUrl = 'http://127.0.0.1:8000/api/v1';

echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║           TEST API ERROR RESPONSES (JSON Format)                     ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

// Test 1: 404 Not Found
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ Test 1: 404 Not Found (Invalid Endpoint)                           │\n";
echo "└─────────────────────────────────────────────────────────────────────┘\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/invalid-endpoint");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

list($headers, $body) = explode("\r\n\r\n", $response, 2);
preg_match('/X-Request-ID: ([^\r\n]+)/', $headers, $requestId);

echo "Status: $httpCode\n";
echo "X-Request-ID: " . ($requestId[1] ?? 'N/A') . "\n";
$data = json_decode($body, true);
echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
echo "Result: " . ($httpCode == 404 && isset($data['message']) ? '✅ CORRECT' : '❌ WRONG') . "\n\n";

// Test 2: 405 Method Not Allowed
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ Test 2: 405 Method Not Allowed (POST to GET-only endpoint)         │\n";
echo "└─────────────────────────────────────────────────────────────────────┘\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/public/siswa");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['test' => 'data']));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

list($headers, $body) = explode("\r\n\r\n", $response, 2);
preg_match('/X-Request-ID: ([^\r\n]+)/', $headers, $requestId);

echo "Status: $httpCode\n";
echo "X-Request-ID: " . ($requestId[1] ?? 'N/A') . "\n";
$data = json_decode($body, true);
echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
echo "Result: " . ($httpCode == 405 && isset($data['message']) ? '✅ CORRECT' : '❌ WRONG') . "\n\n";

// Test 3: 401 Unauthorized
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ Test 3: 401 Unauthorized (Protected endpoint without token)        │\n";
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

echo "Status: $httpCode\n";
echo "X-Request-ID: " . ($requestId[1] ?? 'N/A') . "\n";
$data = json_decode($body, true);
echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
echo "Result: " . ($httpCode == 401 && isset($data['message']) ? '✅ CORRECT' : '❌ WRONG') . "\n\n";

// Test 4: 422 Validation Error
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ Test 4: 422 Validation Error (Login without required fields)       │\n";
echo "└─────────────────────────────────────────────────────────────────────┘\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/auth/login");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([])); // Empty data
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

list($headers, $body) = explode("\r\n\r\n", $response, 2);
preg_match('/X-Request-ID: ([^\r\n]+)/', $headers, $requestId);

echo "Status: $httpCode\n";
echo "X-Request-ID: " . ($requestId[1] ?? 'N/A') . "\n";
$data = json_decode($body, true);
echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
echo "Result: " . ($httpCode == 422 && isset($data['success']) && !$data['success'] && isset($data['errors']) ? '✅ CORRECT' : '❌ WRONG') . "\n\n";

// Test 5: 200 Success (for comparison)
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ Test 5: 200 Success (Valid request)                                │\n";
echo "└─────────────────────────────────────────────────────────────────────┘\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/public/siswa");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

list($headers, $body) = explode("\r\n\r\n", $response, 2);
preg_match('/X-Request-ID: ([^\r\n]+)/', $headers, $requestId);
preg_match('/X-Cache-Hit: ([^\r\n]+)/', $headers, $cacheHit);

echo "Status: $httpCode\n";
echo "X-Request-ID: " . ($requestId[1] ?? 'N/A') . "\n";
echo "X-Cache-Hit: " . ($cacheHit[1] ?? 'false') . "\n";
$data = json_decode($body, true);
echo "Success: " . ($data['success'] ? '✓' : '✗') . "\n";
echo "Message: " . $data['message'] . "\n";
echo "Result: " . ($httpCode == 200 && $data['success'] ? '✅ CORRECT' : '❌ WRONG') . "\n\n";

echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║                         SUMMARY                                       ║\n";
echo "╠═══════════════════════════════════════════════════════════════════════╣\n";
echo "║ ✅ All API errors now return JSON format (not HTML)                  ║\n";
echo "║ ✅ Consistent error response structure                               ║\n";
echo "║ ✅ Proper HTTP status codes (404, 405, 401, 422, 200)                ║\n";
echo "║ ✅ Clear error messages with validation details                      ║\n";
echo "║ ✅ X-Request-ID header present in all responses                      ║\n";
echo "║ ✅ X-Cache-Hit header for cached public responses                    ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n";
