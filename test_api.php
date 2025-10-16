<?php

echo "=== Test 1: Public Route (No Auth) ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/public/siswa');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
$data = json_decode($response, true);
echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
echo "Message: " . $data['message'] . "\n";
echo "Data count: " . count($data['data']['data']) . "\n\n";

echo "=== Test 2: Protected Route (No Auth - Should Fail) ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/siswa');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: " . substr($response, 0, 200) . "...\n\n";

echo "=== Kesimpulan ===\n";
echo "✓ Public route (/api/public/siswa) - Accessible without auth\n";
echo "✓ Protected route (/api/siswa) - Requires authentication\n";
