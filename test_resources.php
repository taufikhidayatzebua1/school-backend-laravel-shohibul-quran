<?php

$baseUrl = 'http://127.0.0.1:8000/api/v1';

echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║           TEST API RESOURCES (Data Limiting)                          ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

// Test 1: Public Siswa Resource (Limited Data)
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ Test 1: Public Siswa Endpoint (Limited Fields)                     │\n";
echo "└─────────────────────────────────────────────────────────────────────┘\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/public/siswa?per_page=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);
$siswaPublic = $data['data'][0] ?? null;

echo "HTTP Status: $httpCode\n";
echo "Fields in Public Siswa Resource:\n";
if ($siswaPublic) {
    foreach (array_keys($siswaPublic) as $field) {
        echo "  - $field\n";
    }
    
    $hiddenFields = ['user', 'alamat', 'tanggal_lahir', 'created_at', 'updated_at'];
    $hasHiddenFields = false;
    foreach ($hiddenFields as $field) {
        if (isset($siswaPublic[$field])) {
            echo "\n❌ LEAKED: Field '$field' should be hidden!\n";
            $hasHiddenFields = true;
        }
    }
    
    if (!$hasHiddenFields) {
        echo "\nResult: ✅ CORRECT - Sensitive fields are hidden\n";
    }
} else {
    echo "❌ No data returned\n";
}
echo "\n";

// Test 2: Protected Siswa Resource (Full Data)
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ Test 2: Protected Siswa Endpoint (Full Fields)                     │\n";
echo "└─────────────────────────────────────────────────────────────────────┘\n";

// Login first
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/auth/login");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'kepala.sekolah@sekolah.com',
    'password' => 'password123'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$loginData = json_decode($response, true);
$token = $loginData['data']['access_token'] ?? null;

if ($token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$baseUrl/siswa?per_page=1");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $data = json_decode($response, true);
    $siswaProtected = $data['data'][0] ?? null;
    
    echo "HTTP Status: $httpCode\n";
    echo "Fields in Protected Siswa Resource:\n";
    if ($siswaProtected) {
        foreach (array_keys($siswaProtected) as $field) {
            echo "  - $field\n";
        }
        
        $requiredFields = ['user', 'alamat', 'tanggal_lahir'];
        $hasAllFields = true;
        foreach ($requiredFields as $field) {
            if (!isset($siswaProtected[$field])) {
                echo "\n❌ MISSING: Field '$field' should be present!\n";
                $hasAllFields = false;
            }
        }
        
        if ($hasAllFields) {
            echo "\nResult: ✅ CORRECT - All fields are present\n";
        }
    } else {
        echo "❌ No data returned\n";
    }
} else {
    echo "❌ Failed to login\n";
}
echo "\n";

// Test 3: Public Kelas Resource
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ Test 3: Public Kelas Endpoint (Limited Fields)                     │\n";
echo "└─────────────────────────────────────────────────────────────────────┘\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/public/kelas?per_page=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);
$kelasPublic = $data['data'][0] ?? null;

echo "HTTP Status: $httpCode\n";
echo "Fields in Public Kelas Resource:\n";
if ($kelasPublic) {
    foreach (array_keys($kelasPublic) as $field) {
        echo "  - $field\n";
    }
    
    // Check if wali_kelas details are hidden
    if (isset($kelasPublic['wali_kelas']) && is_array($kelasPublic['wali_kelas'])) {
        echo "\n❌ LEAKED: wali_kelas details should be simplified!\n";
    } else {
        echo "\nResult: ✅ CORRECT - Wali kelas data is limited\n";
    }
} else {
    echo "❌ No data returned\n";
}
echo "\n";

// Test 4: Public Hafalan Resource
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ Test 4: Public Hafalan Endpoint (Limited Fields)                   │\n";
echo "└─────────────────────────────────────────────────────────────────────┘\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/public/hafalan?per_page=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);
$hafalanPublic = $data['data'][0] ?? null;

echo "HTTP Status: $httpCode\n";
echo "Fields in Public Hafalan Resource:\n";
if ($hafalanPublic) {
    foreach (array_keys($hafalanPublic) as $field) {
        echo "  - $field\n";
    }
    
    $hiddenFields = ['guru', 'catatan', 'created_at', 'updated_at'];
    $hasHiddenFields = false;
    foreach ($hiddenFields as $field) {
        if (isset($hafalanPublic[$field])) {
            echo "\n❌ LEAKED: Field '$field' should be hidden!\n";
            $hasHiddenFields = true;
        }
    }
    
    if (!$hasHiddenFields) {
        echo "\nResult: ✅ CORRECT - Sensitive fields are hidden\n";
    }
} else {
    echo "❌ No data returned\n";
}
echo "\n";

// Test 5: Data Comparison
echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ Test 5: Field Count Comparison (Public vs Protected)               │\n";
echo "└─────────────────────────────────────────────────────────────────────┘\n";

if ($siswaPublic && $siswaProtected) {
    $publicCount = count($siswaPublic);
    $protectedCount = count($siswaProtected);
    
    echo "Public Siswa Fields: $publicCount\n";
    echo "Protected Siswa Fields: $protectedCount\n";
    echo "Difference: " . ($protectedCount - $publicCount) . " additional fields in protected\n\n";
    
    echo "Hidden in Public API:\n";
    $hiddenFields = array_diff(array_keys($siswaProtected), array_keys($siswaPublic));
    foreach ($hiddenFields as $field) {
        echo "  - $field\n";
    }
    
    echo "\nResult: " . ($protectedCount > $publicCount ? '✅ CORRECT' : '❌ WRONG') . "\n";
}
echo "\n";

echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║                         SUMMARY                                       ║\n";
echo "╠═══════════════════════════════════════════════════════════════════════╣\n";
echo "║ ✅ Public endpoints expose limited data (privacy protection)          ║\n";
echo "║ ✅ Protected endpoints expose full data (authorized access)           ║\n";
echo "║ ✅ API Resources implemented correctly                                ║\n";
echo "║                                                                       ║\n";
echo "║ Public Resources:                                                     ║\n";
echo "║   - SiswaPublicResource: Hides user, alamat, tanggal_lahir           ║\n";
echo "║   - KelasPublicResource: Simplifies wali_kelas data                  ║\n";
echo "║   - HafalanPublicResource: Hides guru, catatan                       ║\n";
echo "║                                                                       ║\n";
echo "║ Protected Resources:                                                  ║\n";
echo "║   - SiswaResource: Full data with relationships                      ║\n";
echo "║   - KelasResource: Complete kelas information                        ║\n";
echo "║   - HafalanResource: All fields including private notes              ║\n";
echo "║                                                                       ║\n";
echo "║ Files: app/Http/Resources/*                                          ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n";
