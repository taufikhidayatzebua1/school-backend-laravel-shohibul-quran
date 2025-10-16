<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Carbon\Carbon;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║           TIMEZONE & LOCALE CONFIGURATION TEST               ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Timezone Info
echo "📍 TIMEZONE CONFIGURATION:\n";
echo "   Config Timezone: " . config('app.timezone') . "\n";
echo "   PHP Timezone: " . date_default_timezone_get() . "\n";
echo "   Carbon Timezone: " . Carbon::now()->timezoneName . "\n\n";

// Current Time
echo "🕐 CURRENT TIME:\n";
echo "   PHP date(): " . date('Y-m-d H:i:s') . "\n";
echo "   Carbon::now(): " . Carbon::now()->format('Y-m-d H:i:s') . "\n";
echo "   Carbon (formatted): " . Carbon::now()->translatedFormat('l, d F Y - H:i:s') . "\n\n";

// Locale Info
echo "🌍 LOCALE CONFIGURATION:\n";
echo "   App Locale: " . config('app.locale') . "\n";
echo "   Fallback Locale: " . config('app.fallback_locale') . "\n";
echo "   Faker Locale: " . config('app.faker_locale') . "\n\n";

// Date Examples in Indonesian
echo "📅 DATE EXAMPLES (Indonesian):\n";
Carbon::setLocale('id');
echo "   Today: " . Carbon::now()->translatedFormat('l, d F Y') . "\n";
echo "   Tomorrow: " . Carbon::tomorrow()->translatedFormat('l, d F Y') . "\n";
echo "   Yesterday: " . Carbon::yesterday()->translatedFormat('l, d F Y') . "\n";
echo "   Diff for Humans: " . Carbon::now()->subDays(2)->diffForHumans() . "\n\n";

// Timezone Comparison
echo "🌏 TIMEZONE COMPARISON:\n";
$now = Carbon::now();
echo "   Jakarta (WIB): " . $now->timezone('Asia/Jakarta')->format('H:i:s') . "\n";
echo "   Makassar (WITA): " . $now->timezone('Asia/Makassar')->format('H:i:s') . "\n";
echo "   Jayapura (WIT): " . $now->timezone('Asia/Jayapura')->format('H:i:s') . "\n";
echo "   UTC: " . $now->timezone('UTC')->format('H:i:s') . "\n\n";

echo "✅ Configuration successfully loaded!\n";
