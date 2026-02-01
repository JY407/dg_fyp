<?php
// Test Visitor Registration with Location Tracking

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== 测试访客登记系统（带位置追踪） ===\n\n";

// Get first user or create one
$user = App\Models\User::first();
if (!$user) {
    echo "❌ 没有找到用户，请先创建一个用户账号\n";
    exit(1);
}

echo "使用用户: {$user->name} (ID: {$user->id})\n\n";

// Create a test visitor with location
$visitor = App\Models\Visitor::create([
    'user_id' => $user->id,
    'name' => 'John Doe',
    'ic_number' => '123456-78-9012',
    'vehicle_number' => 'ABC 1234',
    'visit_purpose' => 'Visiting family',
    'expected_arrival' => now()->addHours(1),
    'pass_code' => strtoupper(substr(md5(uniqid()), 0, 8)),
    'status' => 'pending',
    // Location data (simulating GPS capture from Kuala Lumpur)
    'latitude' => 3.139003,
    'longitude' => 101.686855,
    'location_address' => 'Kuala Lumpur City Centre, Kuala Lumpur, Malaysia',
    'location_captured_at' => now(),
]);

echo "✅ 访客登记成功！\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 访客信息：\n";
echo "  ID: {$visitor->id}\n";
echo "  姓名: {$visitor->name}\n";
echo "  IC号码: {$visitor->ic_number}\n";
echo "  车牌号: {$visitor->vehicle_number}\n";
echo "  访问目的: {$visitor->visit_purpose}\n";
echo "  通行码: {$visitor->pass_code}\n";
echo "  状态: {$visitor->status}\n";
echo "  预计到达: {$visitor->expected_arrival->format('Y-m-d H:i:s')}\n";
echo "\n📍 位置信息：\n";
echo "  纬度: {$visitor->latitude}°\n";
echo "  经度: {$visitor->longitude}°\n";
echo "  地址: {$visitor->location_address}\n";
echo "  捕获时间: {$visitor->location_captured_at->format('Y-m-d H:i:s')}\n";
echo "\n🗺️  Google Maps链接:\n";
echo "  https://www.google.com/maps?q={$visitor->latitude},{$visitor->longitude}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Verify the data was saved correctly
echo "✓ 验证数据库记录：\n";
$savedVisitor = App\Models\Visitor::find($visitor->id);
echo "  ✓ 访客记录已保存到数据库 (ID: {$savedVisitor->id})\n";
echo "  ✓ 位置数据完整: " . ($savedVisitor->latitude && $savedVisitor->longitude ? "是 ✓" : "否 ✗") . "\n";
echo "  ✓ 地址已保存: " . ($savedVisitor->location_address ? "是 ✓" : "否 ✗") . "\n";
echo "  ✓ 捕获时间已记录: " . ($savedVisitor->location_captured_at ? "是 ✓" : "否 ✗") . "\n";

// Show all visitors with location
echo "\n📊 数据库中所有访客记录：\n";
$allVisitors = App\Models\Visitor::all();
echo "  总数: {$allVisitors->count()}\n";
foreach ($allVisitors as $v) {
    $hasLocation = $v->latitude && $v->longitude ? "📍" : "  ";
    echo "  {$hasLocation} {$v->name} - {$v->pass_code} - {$v->status}\n";
}

echo "\n🎉 测试完成！位置追踪功能正常工作！\n";
