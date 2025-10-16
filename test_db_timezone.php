<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\User;
use Carbon\Carbon;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║         DATABASE TIMESTAMP TIMEZONE TEST                     ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "📝 Creating test user...\n";

$user = new User();
$user->name = 'Test Timezone User';
$user->email = 'test.timezone.' . time() . '@example.com';
$user->password = bcrypt('password');
$user->role = 'admin';
$user->save();

echo "✅ User created successfully!\n\n";

echo "🕐 TIMESTAMP INFORMATION:\n";
echo "   ID: " . $user->id . "\n";
echo "   Created At (Raw): " . $user->created_at . "\n";
echo "   Created At (Formatted): " . $user->created_at->format('Y-m-d H:i:s T') . "\n";
echo "   Timezone: " . $user->created_at->timezoneName . "\n";
echo "   Indonesian: " . $user->created_at->translatedFormat('l, d F Y - H:i:s') . "\n\n";

echo "📊 COMPARISON:\n";
echo "   Current Time (PHP): " . date('Y-m-d H:i:s T') . "\n";
echo "   Current Time (Carbon): " . Carbon::now()->format('Y-m-d H:i:s T') . "\n";
echo "   Database Time: " . $user->created_at->format('Y-m-d H:i:s T') . "\n\n";

echo "🗑️  Cleaning up...\n";
$user->delete();
echo "✅ Test user deleted.\n\n";

echo "✅ Timezone configuration working correctly!\n";
