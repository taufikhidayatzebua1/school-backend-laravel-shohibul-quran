<?php

$baseUrl = 'http://127.0.0.1:8000/api';

echo "Testing 404 Response Format\n";
echo str_repeat("=", 70) . "\n\n";

// Test 1: Valid endpoint
echo "Test 1: Valid endpoint\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/v1/public/siswa");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "URL: /api/v1/public/siswa\n";
echo "Status: $httpCode\n";
preg_match('/Content-Type: ([^\r\n]+)/', $response, $contentType);
echo "Content-Type: " . ($contentType[1] ?? 'N/A') . "\n\n";

// Test 2: Invalid endpoint (404)
echo "Test 2: Invalid endpoint (should return JSON 404)\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/v1/invalid-endpoint");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "URL: /api/v1/invalid-endpoint\n";
echo "Status: $httpCode\n";
preg_match('/Content-Type: ([^\r\n]+)/', $response, $contentType);
echo "Content-Type: " . ($contentType[1] ?? 'N/A') . "\n";

// Check if response is HTML or JSON
list($headers, $body) = explode("\r\n\r\n", $response, 2);
$isHtml = (strpos($body, '<!DOCTYPE') !== false || strpos($body, '<html') !== false);
$isJson = (strpos($contentType[1] ?? '', 'json') !== false);

echo "Response Type: " . ($isHtml ? '❌ HTML (WRONG!)' : ($isJson ? '✅ JSON (CORRECT)' : '❓ Unknown')) . "\n";
echo "Body Preview: " . substr(trim($body), 0, 100) . "...\n\n";

echo str_repeat("=", 70) . "\n";
echo "PROBLEM: API should return JSON for 404, not HTML page!\n";
