<?php

/**
 * Test API Response with Indonesian Timezone
 */

$baseUrl = 'http://127.0.0.1:8000/api/v1';

// Color codes
$GREEN = "\033[32m";
$CYAN = "\033[36m";
$RESET = "\033[0m";

function makeRequest($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init($url);
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
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
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║       API TIMEZONE TEST - Indonesian Time (WIB)              ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Login
echo "{$CYAN}1. Logging in...{$RESET}\n";
$loginResponse = makeRequest("$baseUrl/auth/login", 'POST', [
    'email' => 'budi.santoso@sekolah.com',
    'password' => 'password123'
]);

if ($loginResponse['code'] === 200) {
    $token = $loginResponse['body']['data']['access_token'];
    $user = $loginResponse['body']['data']['user'];
    
    echo "{$GREEN}✓ Login successful{$RESET}\n";
    echo "   Name: {$user['name']}\n";
    echo "   Role: {$user['role']}\n\n";
    
    // Get Profile
    echo "{$CYAN}2. Getting profile...{$RESET}\n";
    $profileResponse = makeRequest("$baseUrl/auth/profile", 'GET', null, $token);
    
    if ($profileResponse['code'] === 200) {
        $profile = $profileResponse['body']['data'];
        echo "{$GREEN}✓ Profile retrieved{$RESET}\n";
        echo "   Created At: {$profile['created_at']}\n";
        echo "   Updated At: {$profile['updated_at']}\n\n";
    }
    
    // Get Hafalan
    echo "{$CYAN}3. Getting hafalan list...{$RESET}\n";
    $hafalanResponse = makeRequest("$baseUrl/guru/hafalan?per_page=1", 'GET', null, $token);
    
    if ($hafalanResponse['code'] === 200) {
        $hafalan = $hafalanResponse['body']['data'][0] ?? null;
        
        if ($hafalan) {
            echo "{$GREEN}✓ Hafalan retrieved{$RESET}\n";
            echo "   Siswa: {$hafalan['siswa']['nama']}\n";
            echo "   Surah: {$hafalan['nama_surah']} ({$hafalan['juz']})\n";
            echo "   Status: {$hafalan['status']}\n";
            echo "   Created At: {$hafalan['created_at']}\n";
            echo "   Updated At: {$hafalan['updated_at']}\n\n";
        }
    }
    
} else {
    echo "❌ Login failed\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "{$GREEN}✓ All timestamps should show Indonesian timezone (WIB){$RESET}\n";
echo "═══════════════════════════════════════════════════════════════\n";
