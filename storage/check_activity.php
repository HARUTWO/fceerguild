<?php
$vendor = __DIR__ . '/../vendor/autoload.php';
if (! file_exists($vendor)) {
    echo json_encode(['error' => 'vendor/autoload.php not found at ' . $vendor]);
    exit(1);
}
require $vendor;
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Activitylog\Models\Activity;

try {
    $total = Activity::count();
    $acts = Activity::orderBy('created_at','desc')->limit(20)->get()->toArray();
    $prov = Activity::where('subject_type','App\\Models\\Province')->orderBy('created_at','desc')->limit(20)->get()->toArray();
    echo json_encode(['total'=>$total,'recent'=>$acts,'province'=>$prov], JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    echo json_encode(['error' => (string)$e], JSON_PRETTY_PRINT);
}
