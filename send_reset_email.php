<?php
/**
 * Quick Script: Send Password Reset Email
 * Sends reset password email to taufikhizet1350@gmail.com
 */

echo "\n";
echo "========================================\n";
echo "  SENDING PASSWORD RESET EMAIL\n";
echo "========================================\n\n";

// Email to send to
$email = 'taufikhizet1350@gmail.com';

echo "Target Email: {$email}\n";
echo "Server URL: http://127.0.0.1:8000\n\n";

// Send POST request to forgot password API
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://127.0.0.1:8000/api/v1/auth/forgot-password',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode(['email' => $email]),
    CURLOPT_TIMEOUT => 10
]);

echo "Sending request...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($httpCode === 0) {
    echo "\n❌ ERROR: Cannot connect to server!\n";
    echo "Error: {$error}\n\n";
    echo "Please make sure Laravel server is running:\n";
    echo "  php artisan serve\n\n";
    exit(1);
}

echo "\n";
echo "========================================\n";
echo "  RESPONSE\n";
echo "========================================\n\n";
echo "HTTP Status: {$httpCode}\n";
echo "Response Body:\n";
print_r(json_decode($response, true));
echo "\n";

if ($httpCode === 200) {
    echo "✅ SUCCESS!\n\n";
    echo "Email reset password telah dikirim ke:\n";
    echo "  📧 {$email}\n\n";
    echo "Langkah selanjutnya:\n";
    echo "1. Buka Gmail: https://gmail.com\n";
    echo "2. Login dengan: {$email}\n";
    echo "3. Cari email dengan subject: 'Reset Password'\n";
    echo "4. Copy token dari email\n";
    echo "5. Buka: http://127.0.0.1:8000/test-reset-password.html\n";
    echo "6. Paste token dan masukkan password baru\n\n";
} else {
    echo "❌ FAILED!\n\n";
    echo "Please check:\n";
    echo "1. Server is running (php artisan serve)\n";
    echo "2. Email configuration is correct\n";
    echo "3. User exists in database\n\n";
}

echo "========================================\n\n";
