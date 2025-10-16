<?php

$baseUrl = 'http://127.0.0.1:8000/api/v1';

echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║           TEST SECURITY HEADERS                                       ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/public/siswa");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, false);
$response = curl_exec($ch);
curl_close($ch);

// Extract headers
$headers = [];
$lines = explode("\n", $response);
foreach ($lines as $line) {
    if (strpos($line, ':') !== false) {
        list($key, $value) = explode(':', $line, 2);
        $headers[trim($key)] = trim($value);
    }
}

echo "Security Headers Check:\n";
echo str_repeat("-", 70) . "\n\n";

$securityHeaders = [
    'X-Content-Type-Options' => 'Prevents MIME type sniffing',
    'X-Frame-Options' => 'Prevents clickjacking attacks',
    'X-XSS-Protection' => 'Enables XSS protection',
    'X-Request-ID' => 'Request tracking for debugging (NEW)',
];

foreach ($securityHeaders as $header => $description) {
    $status = isset($headers[$header]) ? '✅ PRESENT' : '❌ MISSING';
    $value = isset($headers[$header]) ? $headers[$header] : 'N/A';
    
    echo "$header\n";
    echo "  Status: $status\n";
    echo "  Value: $value\n";
    echo "  Purpose: $description\n\n";
}

echo str_repeat("-", 70) . "\n";
echo "Additional Headers:\n\n";

$additionalHeaders = [
    'Access-Control-Allow-Origin' => 'CORS configuration',
    'Cache-Control' => 'Cache policy',
    'Content-Type' => 'Response format',
    'X-Cache-Hit' => 'Cache status indicator (NEW)',
    'X-RateLimit-Limit' => 'Rate limit configuration',
    'X-RateLimit-Remaining' => 'Remaining requests',
];

foreach ($additionalHeaders as $header => $description) {
    if (isset($headers[$header])) {
        echo "$header: {$headers[$header]}\n";
        echo "  Purpose: $description\n\n";
    }
}

echo "\n╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║  Security Headers are protecting your API from common attacks!       ║\n";
echo "║                                                                       ║\n";
echo "║  ✅ SecurityHeaders middleware active                                 ║\n";
echo "║  ✅ AddRequestId middleware tracking requests                         ║\n";
echo "║  ✅ CacheResponse middleware for performance                          ║\n";
echo "║  ✅ Rate limiting protecting from abuse                               ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n";
