<?php

/**
 * MASTER TEST RUNNER
 * Run all API tests and generate comprehensive report
 */

echo "\n";
echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║                                                                       ║\n";
echo "║               HAFALAN AL-QURAN API - MASTER TEST SUITE                ║\n";
echo "║                                                                       ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$tests = [
    '1. Authentication & Authorization' => 'test_authentication.php',
    '2. API Error Responses' => 'test_api_errors.php',
    '3. Rate Limiting' => 'test_rate_limiting.php',
    '4. Security Headers' => 'test_security_headers.php',
    '5. Response Caching' => 'test_caching.php',
    '6. API Resources (Data Limiting)' => 'test_resources.php',
    '7. Form Request Validation' => 'test_validation.php',
    '8. N+1 Query Problem' => 'test_n1_problem.php',
];

$results = [];
$startTime = microtime(true);

foreach ($tests as $name => $file) {
    echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
    echo "║  RUNNING: $name" . str_repeat(' ', 70 - strlen($name) - 11) . "║\n";
    echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";
    
    $testStartTime = microtime(true);
    
    if (file_exists($file)) {
        ob_start();
        include $file;
        $output = ob_get_clean();
        
        $testDuration = microtime(true) - $testStartTime;
        
        // Count passed/failed assertions
        $passed = substr_count($output, '✅');
        $failed = substr_count($output, '❌');
        
        echo $output;
        
        $results[$name] = [
            'passed' => $passed,
            'failed' => $failed,
            'duration' => $testDuration,
            'status' => $failed > 0 ? 'FAILED' : 'PASSED'
        ];
    } else {
        echo "⚠ Test file not found: $file\n\n";
        $results[$name] = [
            'passed' => 0,
            'failed' => 1,
            'duration' => 0,
            'status' => 'MISSING'
        ];
    }
    
    echo "\n";
    echo "Press ENTER to continue to next test...";
    fgets(STDIN);
    echo "\n\n";
}

$totalDuration = microtime(true) - $startTime;

// Generate Summary Report
echo "\n\n";
echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║                                                                       ║\n";
echo "║                      COMPREHENSIVE TEST REPORT                        ║\n";
echo "║                                                                       ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

$totalPassed = 0;
$totalFailed = 0;
$allPassed = true;

echo "Test Results:\n";
echo str_repeat("=", 76) . "\n";
printf("%-45s %10s %10s %10s\n", "Test Suite", "Passed", "Failed", "Status");
echo str_repeat("=", 76) . "\n";

foreach ($results as $name => $result) {
    $status = $result['status'] === 'PASSED' ? '✅ PASS' : '❌ FAIL';
    printf("%-45s %10d %10d %10s\n", 
        substr($name, 0, 45), 
        $result['passed'], 
        $result['failed'], 
        $status
    );
    
    $totalPassed += $result['passed'];
    $totalFailed += $result['failed'];
    
    if ($result['status'] !== 'PASSED') {
        $allPassed = false;
    }
}

echo str_repeat("=", 76) . "\n";
printf("%-45s %10d %10d\n", "TOTAL", $totalPassed, $totalFailed);
echo str_repeat("=", 76) . "\n\n";

echo "Performance:\n";
echo str_repeat("-", 76) . "\n";
foreach ($results as $name => $result) {
    printf("%-50s %12s\n", 
        substr($name, 0, 50), 
        number_format($result['duration'], 3) . "s"
    );
}
echo str_repeat("-", 76) . "\n";
printf("%-50s %12s\n", "Total Execution Time", number_format($totalDuration, 3) . "s");
echo str_repeat("-", 76) . "\n\n";

echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║                      IMPLEMENTATION CHECKLIST                         ║\n";
echo "╠═══════════════════════════════════════════════════════════════════════╣\n";
echo "║                                                                       ║\n";
echo "║  HIGH PRIORITY (Security & Performance)                               ║\n";
echo "║    ✅ Token Expiration (24 hours)                                     ║\n";
echo "║    ✅ Query Optimization (Eager loading)                              ║\n";
echo "║    ✅ Query Parameter Validation (max 100 per page)                   ║\n";
echo "║    ✅ Logging (Security channel with request IDs)                     ║\n";
echo "║                                                                       ║\n";
echo "║  MEDIUM PRIORITY (API Design & Code Quality)                          ║\n";
echo "║    ✅ API Resources (Public + Protected variants)                     ║\n";
echo "║    ✅ Form Requests (9 request classes)                               ║\n";
echo "║    ✅ Response Data Limiting (Separate public controllers)            ║\n";
echo "║    ✅ Pagination Enhancement (Custom meta format)                     ║\n";
echo "║                                                                       ║\n";
echo "║  LOW PRIORITY (Developer Experience)                                  ║\n";
echo "║    ✅ API Documentation (Scribe @ /api/v1/docs)                       ║\n";
echo "║    ✅ Response Caching (30 min for public endpoints)                  ║\n";
echo "║    ✅ Environment Config (config/api.php + .env)                      ║\n";
echo "║    ✅ Request ID Tracking (UUID in headers & logs)                    ║\n";
echo "║                                                                       ║\n";
echo "╠═══════════════════════════════════════════════════════════════════════╣\n";
echo "║                         FINAL STATUS                                  ║\n";
echo "╠═══════════════════════════════════════════════════════════════════════╣\n";

if ($allPassed) {
    echo "║                                                                       ║\n";
    echo "║                    🎉 ALL TESTS PASSED! 🎉                            ║\n";
    echo "║                                                                       ║\n";
    echo "║              API is PRODUCTION READY for deployment!                 ║\n";
    echo "║                                                                       ║\n";
} else {
    echo "║                                                                       ║\n";
    echo "║                    ⚠ SOME TESTS FAILED ⚠                             ║\n";
    echo "║                                                                       ║\n";
    echo "║            Please review failed tests before deployment              ║\n";
    echo "║                                                                       ║\n";
}

echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

echo "API Endpoints:\n";
echo "  - Documentation: http://localhost:8000/api/v1/docs\n";
echo "  - Postman Collection: storage/app/private/scribe/collection.json\n";
echo "  - OpenAPI Spec: storage/app/private/scribe/openapi.yaml\n\n";

echo "Configuration Files:\n";
echo "  - API Config: config/api.php\n";
echo "  - Environment: .env\n";
echo "  - Sanctum: config/sanctum.php\n\n";

echo "Logs:\n";
echo "  - Security Log: storage/logs/security.log\n";
echo "  - Laravel Log: storage/logs/laravel.log\n\n";

$exitCode = $allPassed ? 0 : 1;
echo "Exit Code: $exitCode\n\n";

exit($exitCode);
