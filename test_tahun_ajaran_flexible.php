<?php

/**
 * Test script untuk validasi fleksibilitas tahun ajaran (semester & tahun)
 * 
 * Testing:
 * 1. Format standar (Ganjil/Genap, YYYY/YYYY)
 * 2. Format numerik (Semester 1/2)
 * 3. Format international (Quarter, Term)
 * 4. Format custom
 * 5. Validation error (string terlalu panjang)
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║     TEST FLEKSIBILITAS TAHUN AJARAN (SEMESTER & TAHUN)       ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

$testCases = [
    [
        'name' => 'Format Standar Indonesia (Ganjil/Genap)',
        'data' => [
            'semester' => 'Ganjil',
            'tahun' => '2025/2026',
            'is_active' => false,
        ],
        'should_pass' => true,
    ],
    [
        'name' => 'Format Standar Indonesia (Genap)',
        'data' => [
            'semester' => 'Genap',
            'tahun' => '2025/2026',
            'is_active' => false,
        ],
        'should_pass' => true,
    ],
    [
        'name' => 'Format Numerik (Semester 1)',
        'data' => [
            'semester' => 'Semester 1',
            'tahun' => '2025/2026',
            'is_active' => false,
        ],
        'should_pass' => true,
    ],
    [
        'name' => 'Format Numerik (Semester 2)',
        'data' => [
            'semester' => 'Semester 2',
            'tahun' => '2025-2026',
            'is_active' => false,
        ],
        'should_pass' => true,
    ],
    [
        'name' => 'Format Quarter',
        'data' => [
            'semester' => 'Quarter 1',
            'tahun' => '2025',
            'is_active' => false,
        ],
        'should_pass' => true,
    ],
    [
        'name' => 'Format Quarter (Short)',
        'data' => [
            'semester' => 'Q2',
            'tahun' => '2025',
            'is_active' => false,
        ],
        'should_pass' => true,
    ],
    [
        'name' => 'Format Term (International)',
        'data' => [
            'semester' => 'Fall Term',
            'tahun' => '2025-2026',
            'is_active' => false,
        ],
        'should_pass' => true,
    ],
    [
        'name' => 'Format Caturwulan',
        'data' => [
            'semester' => 'Caturwulan 1',
            'tahun' => 'TA 2025/2026',
            'is_active' => false,
        ],
        'should_pass' => true,
    ],
    [
        'name' => 'Format Custom',
        'data' => [
            'semester' => 'Periode Januari-Juni',
            'tahun' => 'Academic Year 2025',
            'is_active' => false,
        ],
        'should_pass' => true,
    ],
    [
        'name' => 'Error: Semester terlalu panjang (>50 chars)',
        'data' => [
            'semester' => str_repeat('A', 51), // 51 characters
            'tahun' => '2025/2026',
            'is_active' => false,
        ],
        'should_pass' => false,
        'expected_error' => 'semester.max',
    ],
    [
        'name' => 'Error: Tahun terlalu panjang (>20 chars)',
        'data' => [
            'semester' => 'Ganjil',
            'tahun' => str_repeat('2025/', 5), // 25 characters
            'is_active' => false,
        ],
        'should_pass' => false,
        'expected_error' => 'tahun.max',
    ],
    [
        'name' => 'Error: Semester kosong',
        'data' => [
            'semester' => '',
            'tahun' => '2025/2026',
            'is_active' => false,
        ],
        'should_pass' => false,
        'expected_error' => 'semester.required',
    ],
];

$passed = 0;
$failed = 0;
$testNumber = 1;

foreach ($testCases as $test) {
    echo "📝 Test {$testNumber}: {$test['name']}\n";
    echo str_repeat("-", 65) . "\n";
    
    // Create validator
    $validator = \Illuminate\Support\Facades\Validator::make($test['data'], [
        'semester' => 'required|string|max:50',
        'tahun' => 'required|string|max:20',
        'is_active' => 'nullable|boolean',
    ], [
        'semester.required' => 'Semester wajib diisi.',
        'semester.max' => 'Semester maksimal 50 karakter.',
        'tahun.required' => 'Tahun ajaran wajib diisi.',
        'tahun.max' => 'Tahun ajaran maksimal 20 karakter.',
    ]);
    
    $validationPassed = !$validator->fails();
    
    if ($test['should_pass']) {
        if ($validationPassed) {
            echo "✅ PASSED - Validation berhasil\n";
            echo "   Semester: {$test['data']['semester']}\n";
            echo "   Tahun: {$test['data']['tahun']}\n";
            
            // Try to create in database
            try {
                $tahunAjaran = \App\Models\TahunAjaran::create($test['data']);
                echo "   ✅ Berhasil disimpan ke database (ID: {$tahunAjaran->id})\n";
                $passed++;
            } catch (\Exception $e) {
                echo "   ⚠️ Validation passed tapi gagal save: {$e->getMessage()}\n";
                $failed++;
            }
        } else {
            echo "❌ FAILED - Validation seharusnya berhasil tapi gagal\n";
            echo "   Errors:\n";
            foreach ($validator->errors()->all() as $error) {
                echo "   - {$error}\n";
            }
            $failed++;
        }
    } else {
        // Test case yang seharusnya fail
        if (!$validationPassed) {
            $errors = $validator->errors();
            $hasExpectedError = isset($test['expected_error']) ? 
                $errors->has(explode('.', $test['expected_error'])[0]) : true;
            
            if ($hasExpectedError) {
                echo "✅ PASSED - Validation correctly failed\n";
                echo "   Errors:\n";
                foreach ($errors->all() as $error) {
                    echo "   - {$error}\n";
                }
                $passed++;
            } else {
                echo "❌ FAILED - Validation failed tapi bukan error yang diharapkan\n";
                echo "   Expected error: {$test['expected_error']}\n";
                echo "   Actual errors:\n";
                foreach ($errors->all() as $error) {
                    echo "   - {$error}\n";
                }
                $failed++;
            }
        } else {
            echo "❌ FAILED - Validation seharusnya gagal tapi berhasil\n";
            $failed++;
        }
    }
    
    echo "\n";
    $testNumber++;
}

echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║                      TEST SUMMARY                             ║\n";
echo "╠═══════════════════════════════════════════════════════════════╣\n";
printf("║  Total Tests: %-2d                                            ║\n", count($testCases));
printf("║  Passed: %-2d ✅                                              ║\n", $passed);
printf("║  Failed: %-2d ❌                                              ║\n", $failed);
echo "╠═══════════════════════════════════════════════════════════════╣\n";

if ($failed === 0) {
    echo "║  🎉 SEMUA TEST BERHASIL! FLEKSIBILITAS BEKERJA SEMPURNA!     ║\n";
} else {
    echo "║  ⚠️ ADA TEST YANG GAGAL, PERLU DIPERBAIKI                     ║\n";
}

echo "╚═══════════════════════════════════════════════════════════════╝\n";

// Show sample data from database
echo "\n";
echo "📊 SAMPLE DATA TAHUN AJARAN DARI DATABASE:\n";
echo str_repeat("-", 65) . "\n";

$tahunAjarans = \App\Models\TahunAjaran::orderBy('id', 'desc')->limit(10)->get();

if ($tahunAjarans->count() > 0) {
    foreach ($tahunAjarans as $ta) {
        $activeStatus = $ta->is_active ? '🟢 Active' : '⚪ Inactive';
        echo sprintf(
            "ID: %-2d | Semester: %-20s | Tahun: %-15s | %s\n",
            $ta->id,
            $ta->semester,
            $ta->tahun,
            $activeStatus
        );
    }
} else {
    echo "Belum ada data tahun ajaran.\n";
}
